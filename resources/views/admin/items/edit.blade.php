@extends('admin/include/master')
@section('title') لوحة التحكم | تعديل بيانات العنصر @endsection
@section('content')

<section class="content">
    <div class="row">
      <div class="col-xs-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">تعديل بيانات العنصر </h3>
          <p> {{ $editem->artitle}} </p>
        </div>
         
        {!! Form::open(array('method' => 'patch','files' => true,'url' =>'adminpanel/items/'.$editem->id)) !!}
        <div class="box-body">
            
                  
                  
                  <div class="form-group col-md-6">
                    <label>اسم العنصر </label>
                    <input type="text" class="form-control" name="artitle" placeholder="ادخل اسم العنصر  " value="{{ $editem->artitle }}" required>
                    @if ($errors->has('artitle'))
                        <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('artitle') }}</div>
                    @endif  
                  </div>
                  <div class="form-group col-md-6">
                        <label>الاقسام</label>
                        <select class="form-control"  name="category_id" required>
                            <option value="0" disabled="">اختار القسم</option>
                            @foreach($allcats as $cat)
                                <option value="{{$cat->id}}" @if($cat->id == $editem->category_id) selected @endif> {{$cat->name}} </option>
                            @endforeach
                        </select>
                        @if ($errors->has('category_id'))
                            <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('category_id') }}</div>
                        @endif  
                  </div>

                 

                  <div class="form-group col-md-6">
                      <label> العنوان</label>
                  <input type="text" value="{{$editem->address}}" name="address" class="form-control" placeholder = 'العنوان '>
                      @if ($errors->has('address'))
                          <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('address') }}</div>
                      @endif  
                  </div>

                  <div class="form-group col-md-6">
                    <label>الخصم </label>
                    <input type="number"  value="{{$editem->discount}}" name="discount" class="form-control" placeholder = 'ادخل الخصم' >
                 
                  </div>

                  <div class="col-md-12">
                      <div class="box box-info">
                          <div class="box-header">
                          <h3 class="box-title" > تفاصيل الخصم </h3>
                          </div>
                          <div class="box-body pad">
                              <textarea id="editor1" name="discount_text" rows="10" cols="167" required>{!! $editem->discount_text !!}</textarea>
                              @if ($errors->has('discount_text'))
                                  <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('discount_text') }}</div>
                              @endif
                          </div>
                      </div>
                  </div>


                  

                  <div class="col-md-12">
                      <div class="box box-info">
                          <div class="box-header">
                          <h3 class="box-title" > تفاصيل العنصر</h3>
                          </div>
                          <div class="box-body pad">
                              <textarea id="editor2" name="ardesc" rows="10" cols="167" required>{!! $editem->details !!}</textarea>
                              @if ($errors->has('ardesc'))
                                  <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('ardesc') }}</div>
                              @endif
                          </div>
                      </div>
                  </div>

                   <div class="form-group col-md-12">
                    <label>صور اكثر عن الاعلان [يمكنك رفع اكثر من صورة]</label>
                    <input type="file" name="items[]" multiple>
                    @if ($errors->has('items'))
                        <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('items') }}</div>
                    @endif  
                  </div>

                  <div class="form-group col-md-12">
                      <label>صور العنصر</label>
                      <br>
                      @foreach($adimages as $image)
                      <div style="padding: 2%;" class="col-md-3">
                          <img class="img-thumbnail" style="width:100%; height:10%;" src="{{asset('users/images/'.$image->image)}}" alt="">
                      </div>
                      @endforeach
                  </div>
               
                <div class="box-footer">
                  <button style="width: 20%;margin-right: 40%;" type="submit" id="submit1" class="btn btn-primary">تعديل</button>
                </div>
        {!! Form::close() !!}
        </div> 
      </div>  
    </div> 
</section>    

<script type="text/javascript">

    $(document).ready(function () {
        $('#itme_offer').change(function() {
         if($(this).val() == 1)  
         {
            $("#discountprice").css('display', 'block');  
         } else {  
            $("#discountprice").css('display', 'none');   
         }  
        });
    });

</script>
@endsection 
