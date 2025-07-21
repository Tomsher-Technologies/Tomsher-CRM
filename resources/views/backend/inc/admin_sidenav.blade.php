<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <a href="{{ route('admin.dashboard') }}" class="d-block text-left">
                <img class="mw-100" height="100" src="{{ asset('assets/images/logow.png') }}" 
                        alt="{{ get_setting('site_name') }}">
            </a>
        </div>
        <div class="aiz-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control border-0 form-control-sm" type="text" name="" placeholder="{{  trans('messages.search_in_menu') }}" id="menu-search" onkeyup="menuSearch()">
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="aiz-side-nav-link">
                            <i class="las la-home aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{  trans('messages.dashboard') }}</span>
                        </a>
                    </li>

                    @can('view_followup_calendar')
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('followups.calendar') }}" class="aiz-side-nav-link">
                                <i class="las la-calendar aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Follow-ups Calendar</span>
                            </a>
                        </li>
                    @endcan

                    @canany(['manage_customers'])
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-users aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Customers</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('add_customer')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('customers.create') }}"
                                            class="aiz-side-nav-link {{ areActiveRoutes(['customers.create']) }}">
                                            <span class="aiz-side-nav-text">Add New Customer</span>
                                        </a>
                                    </li>
                                @endcan
                                
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('customers.index') }}"
                                        class="aiz-side-nav-link {{ areActiveRoutes(['customers.index', 'customers.edit', 'customers.show']) }}">
                                        <span class="aiz-side-nav-text">All Customers</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcanany

                    @canany(['manage_enquiries'])
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-envelope-open aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Enquiries</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('add_enquiries')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('enquiries.create') }}"
                                            class="aiz-side-nav-link {{ areActiveRoutes(['enquiries.create']) }}">
                                            <span class="aiz-side-nav-text">Add New Enquiry</span>
                                        </a>
                                    </li>
                                @endcan
                                
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('enquiries.index') }}"
                                        class="aiz-side-nav-link {{ areActiveRoutes(['enquiries.index', 'enquiries.edit', 'enquiries.show']) }}">
                                        <span class="aiz-side-nav-text">All Enquiries</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcanany

                    @canany(['manage_followups'])
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-calendar aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Follow-ups</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('add_followups')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('followups.create') }}"
                                            class="aiz-side-nav-link {{ areActiveRoutes(['followups.create']) }}">
                                            <span class="aiz-side-nav-text">Add New Follow-up</span>
                                        </a>
                                    </li>
                                @endcan
                                
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('followups.index') }}"
                                        class="aiz-side-nav-link {{ areActiveRoutes(['followups.index', 'followups.edit', 'followups.show']) }}">
                                        <span class="aiz-side-nav-text">All Follow-ups</span>
                                    </a>
                                </li>

                                {{-- @can('view_followup_calendar')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('followups.calendar') }}"
                                            class="aiz-side-nav-link {{ areActiveRoutes(['followups.calendar']) }}">
                                            <span class="aiz-side-nav-text">Follow-up Calendar</span>
                                        </a>
                                    </li>
                                @endcan
                                 --}}
                            </ul>
                        </li>
                    @endcanany

                    @canany(['manage_projects'])
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-cogs aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Projects</span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                @can('add_project')
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('projects.create') }}"
                                            class="aiz-side-nav-link {{ areActiveRoutes(['projects.create']) }}">
                                            <span class="aiz-side-nav-text">Add New Project</span>
                                        </a>
                                    </li>
                                @endcan
                                
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('projects.index') }}"
                                        class="aiz-side-nav-link {{ areActiveRoutes(['projects.index', 'projects.edit', 'projects.show']) }}">
                                        <span class="aiz-side-nav-text">All Projects</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcanany


                    @canany(['manage_enquiry_source'])
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('enquiry_sources.index') }}" class="aiz-side-nav-link">
                                <i class="las la-inbox aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Enquiry Sources</span>
                            </a>
                        </li>
                    @endcanany
                    
                    @canany(['manage_industries'])
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('industries.index') }}" class="aiz-side-nav-link">
                                <i class="las la-industry aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Industries</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['manage_project_type'])
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('project_category.index') }}" class="aiz-side-nav-link">
                                <i class="las la-stream aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Project Categories</span>
                            </a>
                        </li>
                    @endcanany

                    @canany(['manage_technologies'])
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('technologies.index') }}" class="aiz-side-nav-link">
                                <i class="las la-code aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">Technologies</span>
                            </a>
                        </li>
                    @endcanany
                    
                {{-- @canany(['upload_files'])
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('uploaded-files.index') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['uploaded-files.create']) }}">
                            <i class="las la-folder-open aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ trans('messages.uploaded_files') }}</span>
                        </a>
                    </li>
                @endcanany --}}

                <!-- Staffs -->
                
                @canany(['manage_staffs'])
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-users aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{  trans('messages.staffs') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @can('add_staff')
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('staffs.create') }}"
                                        class="aiz-side-nav-link {{ areActiveRoutes(['staffs.create']) }}">
                                        <span class="aiz-side-nav-text">{{ trans('messages.add_new_staffs') }}</span>
                                    </a>
                                </li>
                            @endcan
                            
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('staffs.index') }}"
                                    class="aiz-side-nav-link {{ areActiveRoutes(['staffs.index', 'staffs.edit']) }}">
                                    <span class="aiz-side-nav-text">{{  trans('messages.all_staffs') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcanany

                @canany(['manage_roles'])
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-user-tie aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ trans('messages.roles_permissions')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('roles.create') }}" class="aiz-side-nav-link {{ areActiveRoutes(['roles.create']) }}">
                                    <span class="aiz-side-nav-text">{{ trans('messages.add_new_role')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('roles.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['roles.index','roles.edit']) }}">
                                    <span class="aiz-side-nav-text">{{ trans('messages.all_roles')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcanany
              
               
            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->
