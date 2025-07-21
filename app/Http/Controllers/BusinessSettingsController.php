<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\HomePoints;
use Illuminate\Support\Facades\Storage;
use Artisan;
// use CoreComponentRepository;

class BusinessSettingsController extends Controller
{
    public function general_setting(Request $request)
    {
        // CoreComponentRepository::instantiateShopRepository();
        //CoreComponentRepository::initializeCache();
    	return view('backend.setup_configurations.general_settings');
    }

    public function update(Request $request)
    {
       
        if(!empty($request->types)){
            foreach ($request->types as $key => $type) {
                if($type == 'site_name'){
                    $this->overWriteEnvFile('APP_NAME', $request[$type]);
                }
                if($type == 'timezone'){
                    $this->overWriteEnvFile('APP_TIMEZONE', $request[$type]);
                }
                else {
                    $lang = $request->has('lang') ? $request->lang : NULL;;
                    if(gettype($type) == 'array'){
                        $lang = array_key_first($type);
                        $type = $type[$lang];
                        $business_settings = BusinessSetting::where('type', $type)->where('lang',$lang)->first();
                    }else{
                        $business_settings = BusinessSetting::where('type', $type)->first();
                    }
    
                   
                    if($business_settings!=null){
                        if(gettype($request[$type]) == 'array'){
                            $business_settings->value = json_encode($request[$type]);
                        }
                        else {
                            $business_settings->value = $request[$type];
                        }
                        $business_settings->lang = $lang;
                        $business_settings->save();
                    }
                    else{
                        $business_settings = new BusinessSetting;
                        $business_settings->type = $type;
                        if(gettype($request[$type]) == 'array'){
                            $business_settings->value = json_encode($request[$type]);
                        }
                        else {
                            $business_settings->value = $request[$type];
                        }
                        $business_settings->lang = $lang;
                        $business_settings->save();
                    }
                }
            }
        }

        $id = $request->has('page_id') ? $request->page_id : null;
         
        if($id != null){

            // echo '<pre>';
            // print_r($request->all());
            // die;

            $page = Page::findOrFail($id);
            if ($page) {
                $page_translation                           = PageTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'page_id' => $page->id]);
                if($request->has('title')){
                    $page_translation->title                = $request->title;
                }
                if($request->has('content')){
                    $page_translation->content              = $request->content;
                }
                if($request->has('sub_title')){
                    $page_translation->sub_title            = $request->sub_title;
                }
                if($request->has('title1')){
                    $page_translation->title1                = $request->title1;
                }
                if($request->has('title2')){
                    $page_translation->title2                = $request->title2;
                }
                if($request->has('title3')){
                    $page_translation->title3                = $request->title3;
                }
                if($request->has('heading1')){
                    $page_translation->heading1             = $request->heading1;
                }
                if($request->has('content1')){
                    $page_translation->content1             = $request->content1;
                }
                if($request->has('heading2')){
                    $page_translation->heading2             = $request->heading2;
                }
                if($request->has('content2')){
                    $page_translation->content2             = $request->content2;
                }
                if($request->has('heading3')){
                    $page_translation->heading3             = $request->heading3;
                }
                if($request->has('content3')){
                    $page_translation->content3             = $request->content3;
                }
                if($request->has('content4')){
                    $page_translation->content4             = $request->content4;
                }
                if($request->has('content5')){
                    $page_translation->content5             = $request->content5;
                }
                
                if($request->has('content6')){
                    $page_translation->content6             = $request->content6;
                }
                if($request->has('heading4')){
                $page_translation->heading4                 = $request->heading4;
                }
                if($request->has('heading5')){
                    $page_translation->heading5             = $request->heading5;
                }
                if($request->has('heading6')){
                    $page_translation->heading6             = $request->heading6;
                }
                if($request->has('heading7')){
                    $page_translation->heading7             = $request->heading7;
                }
                if($request->has('heading8')){
                    $page_translation->heading8             = $request->heading8;
                }
                if($request->has('heading9')){
                    $page_translation->heading9             = $request->heading9;
                }

                if($request->has('heading10')){
                    $page_translation->heading10             = $request->heading10;
                }
                if($request->has('heading11')){
                    $page_translation->heading11             = $request->heading11;
                }
                if($request->has('heading12')){
                    $page_translation->heading12             = $request->heading12;
                }
                if($request->has('heading13')){
                    $page_translation->heading13             = $request->heading13;
                }
                if($request->has('heading14')){
                    $page_translation->heading14             = $request->heading14;
                }
                if($request->has('heading15')){
                    $page_translation->heading15             = $request->heading15;
                }
                if($request->has('heading16')){
                    $page_translation->heading16             = $request->heading16;
                }
                if($request->has('heading17')){
                    $page_translation->heading17             = $request->heading17;
                }
                if($request->has('heading18')){
                    $page_translation->heading18             = $request->heading18;
                }
                if($request->has('heading19')){
                    $page_translation->heading19             = $request->heading19;
                }
                
                if($request->has('meta_title')){
                    $page_translation->meta_title           = $request->meta_title;
                }

                if($request->has('meta_description')){
                    $page_translation->meta_description     = $request->meta_description;
                }
                if($request->has('og_title')){
                    $page_translation->og_title             = $request->og_title;
                }
                if($request->has('og_description')){
                    $page_translation->og_description       = $request->og_description;
                }
                if($request->has('twitter_title')){
                    $page_translation->twitter_title        = $request->twitter_title;
                }
                if($request->has('twitter_description')){
                    $page_translation->twitter_description  = $request->twitter_description;
                }
                if($request->has('keywords')){
                    $page_translation->keywords             = $request->keywords;
                }
                if($request->has('image1')){
                    $page_translation->image1               = $request->image1;
                }

                if($request->has('points')){
                    $datass = $request->input('points');
            
                    if($datass){
                        $page_translation->content5             = json_encode($datass);
                    }
                }
                

                $page_translation->save();
    
                if($request->has('image11')){
                    $page->image1               = $request->image11;
                }
                if($request->has('image22')){
                    $page->image2               = $request->image22;
                }
                if($request->has('image33')){
                    $page->image3               = $request->image33;
                }
                if($request->has('image44')){
                    $page->image4               = $request->image44;
                }
                if($request->has('image55')){
                    $page->image               = $request->image55;
                }
                $page->save();
            }

            $data = $request->input('home_points');
            
            if($data){
                HomePoints::truncate();
                foreach ($data as $point) {
                    if($point['title'] != NULL){
                        HomePoints::create([
                            'title' => $point['title'],
                            'image' => $point['icon']
                        ]);
                    }
                }
            }

            
            $photos = [];
            if ($request->hasfile('images')) {
                if ($page->image == null) {
                    $count = 1;
                    $old_photos = [];
                } else {
                    $old_photos = explode(',', $page->image);
                    $count = count($old_photos) + 1;
                }

                foreach ($request->file('images') as $key => $file) {
                    $photos[] = uploadImage('page', $file, 'image_'.$count+$key);
                }
                $page->image = implode(',', array_merge($old_photos, $photos));
                $page->save();
            }

            if ($request->hasfile('video')) {
                if($page->video != NULL){
                    $filePath = str_replace('storage/', '', $page->video);
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
                $fileName = time() . '_Video';
                $video = uploadImage('page', $request->video, $fileName);
                $page->video = $video;
                $page->save();
            }

            if ($request->hasfile('image')) {
                if($page->image != NULL){
                    $filePath = str_replace('storage/', '', $page->image);
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
                $fileName = time() . '_Image';
                $photo = uploadImage('page', $request->image, $fileName);
                $page->image = $photo;
                $page->save();
            }

            if ($request->hasfile('image1')) {
                if($page->image1 != NULL){
                    $filePath = str_replace('storage/', '', $page->image1);
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
                $fileName = time() . '_Image';
                $photo = uploadImage('page', $request->image1, $fileName);
                $page->image1 = $photo;
                $page->save();
            }
        }

        Artisan::call('cache:clear');

        flash(trans("messages.settings").' '.trans("messages.updated_msg"))->success();
        return back();
    }

    

    public function shipping_configuration(Request $request){
        return view('backend.setup_configurations.shipping_configuration.index');
    }

    public function shipping_configuration_update(Request $request){
        $business_settings = BusinessSetting::where('type', $request->type)->first();
        $business_settings->value = $request[$request->type];
        $business_settings->save();

        Artisan::call('cache:clear');
        return back();
    }
}
