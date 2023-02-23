@extends('layouts/contentLayoutMaster')

@section('title', 'Kiểm tra giả lập')
@section('vendor-style')

@endsection
@section('page-style')

@endsection

@section('main')
    <section>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <iframe
                    width="100%"
                    style="height: 70vh"
                    src="https://phet.colorado.edu/sims/html/number-line-distance/latest/number-line-distance_vi.html"
                    frameborder="0">
                </iframe>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
@endsection

@push('scripts')
<script>

</script>
@endpush
