<div class="m-r-20 m-tb-5" id="coupon-group">
  <div class="flex-w flex-m">
    <input class="stext-104 cl2 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5 {{ Session::has('error_discount')?'has-error':'' }}" 
    id="coupon-value" {{ ($plugin['permission'])?'':'disabled' }} type="text" name="coupon" placeholder="{{ trans('cart.coupon') }}">  

    <div class="stext-101 text-danger dis-none cl2 size-118 bg8 bor13 hov-btn3 trans-04 pointer" id="removeCoupon" title="{{ trans('cart.remove_coupon') }}">
      <i class="zmdi zmdi-close fs-25"></i>
    </div>

    <div class="{{ ($plugin['permission'])?'':'disabled' }} flex-c-m stext-101 cl2 size-119 bg8 bor13 hov-btn3 m-l-10 trans-04 pointer m-tb-5" 
        {!! ($plugin['permission'])?'id="coupon-button"':'' !!}  loading="<i class='fa fa-spinner fa-spin'></i>">
        {{ trans('cart.apply') }}
    </div> 

  </div>

  <div class="coupon-msg  {{ Session::has('error_discount')?'text-danger':'' }}">{{ Session::has('error_discount')?Session::get('error_discount'):'' }}</div>

</div>

@push('styles')
<style type="text/css">
  .has-error input,.has-error #coupon-button{
    border-color: #dc3545!important;
  }
</style>
@endpush