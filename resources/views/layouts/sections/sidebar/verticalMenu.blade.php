<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="/" class="app-brand d-flex align-items-center sidebar-brand border-end">
            <img src="{{ asset('assets/img/icons/logo.png') }}" alt="Boxleo Deals Logo" class="w-60 h-auto app-logo me-2">
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @if (isset($menuData[0]) && isset($menuData[0]->menu))
            @foreach ($menuData[0]->menu as $menu)
                {{-- Adding active and open class if child is active --}}

                {{-- Menu Headers --}}
                @if (isset($menu->menuHeader))
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                    </li>
                @else
                    {{-- Active Menu Method --}}
                    @php
                        $activeClass = null;
                        $currentRouteName = Route::currentRouteName();

                        if ($currentRouteName === $menu->slug) {
                            $activeClass = 'active';
                        } elseif (isset($menu->submenu)) {
                            if (gettype($menu->slug) === 'array') {
                                foreach ($menu->slug as $slug) {
                                    if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                                        $activeClass = 'active open';
                                    }
                                }
                            } else {
                                if (str_contains($currentRouteName, $menu->slug) && strpos($currentRouteName, $menu->slug) === 0) {
                                    $activeClass = 'active open';
                                }
                            }
                        }
                    @endphp

                    {{-- Main Menu Item --}}
                    <li class="menu-item {{ $activeClass }}">
                        <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                            class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                            @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif
                            @if (isset($menu->submenu)) onclick="toggleDropdown(event)" @endif>
                            @isset($menu->icon)
                                <i class="{{ $menu->icon }}"></i>
                            @endisset
                            <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
                            @isset($menu->badge)
                                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
                            @endisset
                        </a>

                        {{-- Submenu --}}
                        @isset($menu->submenu)
                            <ul class="submenu" style="display: none;">
                                @foreach ($menu->submenu as $submenu)
                                    <li class="submenu-item {{ $activeClass }}">
                                        <a href="{{ url($submenu->url) }}" class="menu-link">
                                            @if (isset($submenu->icon))
                                                <i class="{{ $submenu->icon }}"></i>
                                            @endif
                                            <div>{{ __($submenu->name) }}</div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endisset
                    </li>
                @endif
            @endforeach
        @else
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">No Menu Available</span>
            </li>
        @endif
    </ul>
</aside>

<script>
    function toggleDropdown(event) {
        event.preventDefault();
        const parentLi = event.currentTarget.parentElement;
        const submenu = parentLi.querySelector('.submenu');

        if (submenu) {
            const isVisible = submenu.style.display === 'block';
            submenu.style.display = isVisible ? 'none' : 'block';
        }
    }
</script>
