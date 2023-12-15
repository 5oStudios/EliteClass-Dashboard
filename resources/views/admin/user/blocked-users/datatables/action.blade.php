@php
    $data = null;
    $webhook = \App\WebhookFPJS::where('visitor_id', $fingerprint['fpjsid'])->first();
    
    if (isset($webhook)) {
        $data = json_decode($webhook->object_data);
    }
@endphp

<div class="dropdown">
    <button class="btn btn-round btn-outline-primary" type="button" id="CustomdropdownMenuButton1" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"><i class="feather icon-more-vertical-"></i></button>
    <div class="dropdown-menu" aria-labelledby="CustomdropdownMenuButton1">
        <button type="button" class="dropdown-item" data-toggle="modal"
            data-target="#exampleStandardModal{{ $id }}">
            <i class="feather icon-eye mr-2"></i>{{ __('View') }}
        </button>
    </div>
</div>


<!-- Modal start -->
<div class="modal fade" id="exampleStandardModal{{ $id }}" data-backdrop="static" data-keyboard="false"
    tabindex="-1" aria-labelledby="exampleStandardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleSmallModalLabel">{{ __('Device info') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($data)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card" style="background-color: #506fe429">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title m-0">Browser</h5>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <span
                                                class="py-2 px-3 mr-2 badge badge-pill badge-info">{{ $data?->browserDetails?->browserName }}</span>
                                            <span
                                                class="py-2 px-3 mr-2 badge badge-pill badge-info">{{ $data?->browserDetails?->browserFullVersion }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card" style="background-color: #506fe429">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title m-0">OS</h5>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <span
                                                class="py-2 px-3 mr-2 badge badge-pill badge-info">{{ $data?->browserDetails?->os }}</span>
                                            <span
                                                class="py-2 px-3 mr-2 badge badge-pill badge-info">{{ $data?->browserDetails?->osVersion }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card" style="background-color: #506fe429">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title m-0">Device</h5>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <span
                                                class="py-2 px-3 mr-2 badge badge-pill badge-info">{{ $data?->browserDetails?->device }}</span>
                                            {{ $data?->browserDetails?->osVersion }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card" style="background-color: #506fe429">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title m-0">Location</h5>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="d-flex">
                                                <span
                                                    class="py-2 px-3 mr-2 badge badge-pill badge-info">{{ $data?->ipLocation?->country->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="card" style="background-color: #506fe429">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title m-0">User Agent</h5>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <p class="">{{ $data?->browserDetails?->userAgent }}</p>
                                            {{ $data?->browserDetails?->osVersion }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div id="map-id{{ $id }}" style="height: 280px; width: 100%;">
                            </div>
                        </div>
                    </div>
                @else
                    {{ __('No Data Found') }}
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Model ended -->

<script>
    $(function() {
        let map = L.map('map-id{{ $id }}').setView([parseFloat(
                "{{ $data?->ipLocation?->latitude }}"),
            parseFloat(
                "{{ $data?->ipLocation?->longitude }}")
        ], 13);

        let customIcon = L.icon({
            iconUrl: "{{ url('images/icons/app-download-ios.png') }}",
            iconSize: [32, 32]
        });

        let marker = L.marker([parseFloat("{{ $data?->ipLocation?->latitude }}"), parseFloat(
            "{{ $data?->ipLocation?->longitude }}")], {
            // icon: customIcon
        }).addTo(map);

        // marker.bindPopup("Hello, this is a marker!").openPopup();
        // L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //     attribution: ''
        // }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        }).addTo(map);

        $('#exampleStandardModal{{ $id }}').on('shown.bs.modal', function() {
            map.invalidateSize();
        });
    });
</script>
