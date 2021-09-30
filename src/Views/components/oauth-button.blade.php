<div>
    <a href="{{ $url }}" class="rounded {{ $icon ? 'py-3' : 'py-2' }} px-4 bg-hubspot-lorax hover:bg-hubspot-sorbet text-hubspot-olaf">
        @if( $icon )
            <span class="inline">
                <svg id="Outlines_-_White" data-name="Outlines - White" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 242.04 257" width="30" height="35" class="inline-flex align-middle"><defs><style>.cls-1{fill:none;}.cls-2{clip-path:url(#clip-path);}.cls-3{fill:#fff;}</style><clipPath id="clip-path"><rect class="cls-1" x="43.12" y="46.67" width="155.93" height="163.82"/></clipPath></defs><title>sprocket-web-white</title><g id="Logo_-_White" data-name="Logo - White"><g class="cls-2"><g class="cls-2"><path class="cls-3" d="M162.65,100.84V81.62a14.79,14.79,0,0,0,8.53-13.34v-.44a14.83,14.83,0,0,0-14.78-14.78H156a14.83,14.83,0,0,0-14.78,14.78v.44a14.8,14.8,0,0,0,8.53,13.34v19.22a41.93,41.93,0,0,0-19.93,8.77L77,68.55a16.84,16.84,0,1,0-7.89,10.26L121,119.21a42,42,0,0,0,.64,47.41l-15.79,15.79a13.56,13.56,0,0,0-3.92-.64,13.7,13.7,0,1,0,13.7,13.7,13.52,13.52,0,0,0-.64-3.91l15.62-15.62a42.12,42.12,0,1,0,32-75.1m-6.47,63.22a21.61,21.61,0,1,1,21.61-21.61,21.61,21.61,0,0,1-21.61,21.61"/></g></g></g></svg>
            </span>
        @endif
        <span class="font-bold inline">
            {{ $copy }}
        </span>
    </a>
</div>
