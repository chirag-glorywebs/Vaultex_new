{{-- Aside --}}

@php
    $kt_logo_image = 'main_logo.png';
@endphp

@if (config('layout.brand.self.theme') === 'light')
    {{--@php $kt_logo_image = 'logo-dark.png' @endphp--}}
    @php $kt_logo_image = 'main_logo.png' @endphp
@elseif (config('layout.brand.self.theme') === 'dark')
    @php $kt_logo_image = 'main_logo.png' @endphp
@endif

<div class="aside aside-left {{ Metronic::printClasses('aside', false) }} d-flex flex-column flex-row-auto" id="kt_aside">

    {{-- Brand --}}
    <div class="brand flex-column-auto {{ Metronic::printClasses('brand', false) }}" id="kt_brand">
        <div class="brand-logo">
            <a href="{{ url('/') }}">
                <img alt="{{ config('app.name') }}" src="{{ asset('media/logos/'.$kt_logo_image) }}"/>
            </a>
        </div>

        @if (config('layout.aside.self.minimize.toggle'))
            <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                {{ Metronic::getSVG("media/svg/icons/Navigation/Angle-double-left.svg", "svg-icon-xl") }}
            </button>
        @endif

    </div>

    {{-- Aside menu --}}
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

        @if (config('layout.aside.self.display') === false)
            <div class="header-logo">
                <a href="{{ url('/') }}">
                    <img alt="{{ config('app.name') }}" src="{{ asset('media/logos/'.$kt_logo_image) }}"/>
                </a>
            </div>
        @endif

        <div
            id="kt_aside_menu"
            class="aside-menu my-4 {{ Metronic::printClasses('aside_menu', false) }}"
            data-menu-vertical="1"
            {{ Metronic::printAttrs('aside_menu') }}>

            @if (Auth::user())               
                @if(Auth::user()->user_role== Helper::getRollId('SALES'))
                    @php
                    $items = config('menu_aside.items');
                    $displayMenus = [];
                    @endphp
                    @foreach ($items as $key => $value)                     
                        @if(isset($value['seller_access']) && $value['seller_access'])
                            @php                                
                                $displayMenus[] = $value;          
                            @endphp
                        @endif    
                    @endforeach
                    @php                        
                        $menuItems = $displayMenus; //['items' => $displayMenus];
                    @endphp
                    <ul class="menu-nav {{ Metronic::printClasses('aside_menu_nav', false) }}">
                    {{ Menu::renderVerMenu($menuItems) }}
                    </ul>
                @else
                    <ul class="menu-nav {{ Metronic::printClasses('aside_menu_nav', false) }}">
                    {{ Menu::renderVerMenu(config('menu_aside.items')) }}               
                    </ul>
                @endif
            @endif

            {{-- <ul class="menu-nav {{ Metronic::printClasses('aside_menu_nav', false) }}">
                {{ Menu::renderVerMenu(config('menu_aside.items')) }}
            </ul> --}}
        </div>
    </div>

</div>
