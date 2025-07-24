<?php

use App\Models\BusinessSetting;
use App\Utility\CategoryUtility;
use App\Models\EnquiryStatus;
use App\Models\EnquiryFollowup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

// use DB;

function getMenu($id)
{
    // Cache::forget('menu_6');
    return Cache::rememberForever('menu_' . $id,  function () use ($id) {
        $menu = Menu::get($id);
        $menu_real = array();
        foreach ($menu as $key => $m) {
            $menu_real[$key] = $m;
            if ($m['img_1']) {
                $menu_real[$key]['img_1_src'] = uploaded_asset($m['img_1']);
            }
            if ($m['img_2']) {
                $menu_real[$key]['img_2_src'] = uploaded_asset($m['img_2']);
            }
            if ($m['img_3']) {
                $menu_real[$key]['img_3_src'] = uploaded_asset($m['img_3']);
            }

            if ($m['brands'] !== null) {
                $brand_ids = explode(',', $m['brands']);
                $brands = Brand::whereIn('id', $brand_ids)->select(['id', 'name', 'logo', 'slug'])->with('logoImage', function ($query) {
                    return $query->select(['id', 'file_name']);
                })->get();

                $menu_real[$key]['brands'] = $brands;
            }
        }
        return $menu_real;
    });
}


if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = '//' . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}


if (!function_exists('getFileBaseURL')) {
    function getFileBaseURL()
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return env('AWS_URL') . '/';
        } else {
            return app('url')->asset('storage') . '/';
            // return getBaseURL();
        }
    }
}


if (!function_exists('get_setting')) {
    function get_setting($key, $default = null, $lang = false)
    {
        $settings = Cache::remember('business_settings', 86400, function () {
            return BusinessSetting::all();
        });

        if ($lang == false) {
            $setting = $settings->where('type', $key)->first();
        } else {
            $setting = $settings->where('type', $key)->where('lang', $lang)->first();
            $setting = !$setting ? $settings->where('type', $key)->first() : $setting;
        }
        return $setting == null ? $default : $setting->value;
    }
}

if (!function_exists('static_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function static_asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

//return file uploaded via uploader
if (!function_exists('uploaded_asset')) {
    function uploaded_asset($id)
    {
        if ($id && ($asset = \App\Models\Upload::find($id)) != null) {
            return $asset->external_link == null ? storage_asset($asset->file_name) : $asset->external_link;
        }

        return app('url')->asset('assets/img/placeholder.jpg');
    }
}



//highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

if (!function_exists('storage_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function storage_asset($path, $secure = null)
    {
        return app('url')->asset('storage/' . $path, $secure);
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}


function cleanSKU($sku)
{
    $sku = str_replace(' ', '', $sku);
    $sku = preg_replace('/[^a-zA-Z0-9_-]/', '', $sku);
    return $sku;
}

function uploadImage($type, $imageUrl, $filename = null){
    $data_url = '';

    // try {
    $ext = $imageUrl->getClientOriginalExtension();
    
    if($type == 'page'){
        $path = 'pages/';
    }else{
        $path = 'others/';
    }
    
    $filename = $path . $filename . '.' . $ext;

    $imageContents = file_get_contents($imageUrl);

    // Save the original image in the storage folder
    Storage::disk('public')->put($filename, $imageContents);
    $data_url = Storage::url($filename);
    
    return $data_url;
}

function getDirection()
{
    if (getActiveLanguage() == 'ar') {
        return 'rtl';
    }
    return 'ltr';
}

function getActiveLanguage()
{
    if (Session::exists('locale')) {
        return Session::get('locale');
    }
    return 'en';
}

function getEnquiryStatuses(){
    $stutuses = EnquiryStatus::where('is_active', 1)->orderBy('sort_order','asc')->get()
                                ->mapWithKeys(function ($status) {
                                    return [
                                        $status->status_key => [
                                            'label' => $status->label,
                                            'bg' => $status->bg,
                                            'filter_color' => $status->filter_color,
                                            'list_color' => $status->list_color
                                        ]
                                    ];
                                })
                                ->toArray();
    return $stutuses;
}

function getDueFutureFollowups($enquiryId){
    $followups = EnquiryFollowup::where('enquiry_id', $enquiryId)
                    ->where('status', 'pending')
                    ->count();
    return $followups;
}
