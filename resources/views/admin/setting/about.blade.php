@extends('admin.include.master')
@section('title') لوحة التحكم | مميزات التطبيق  @endsection
@section('content')
  
<section class="content">
        <div class="row">
                <div class="col-xs-12">
              {{ Form::open(array('method' => 'patch','url' => 'adminpanel/about/'.$policy->id )) }}
                <div class="box-body">
                    <!-- editor -->
                    <div class="col-md-12">
                            <div class="box box-info">
                                <div class="box-header">
                                <h3 class="box-title" >
                                 مميزات التطبيق 
                                </h3>
                                </div>
                                <div class="box-body pad">
                                    <textarea id="editor1" name="features" rows="10" cols="80" required>{{$policy->features}}</textarea>
                                    @if ($errors->has('features'))
                                        <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('features') }}</div>
                                    @endif
                                </div>
                               
                            </div>
                             <div class="box box-info">
                                <div class="box-header">
                                <h3 class="box-title" >
                                 تفاصيل مميزات التطبيق 
                                </h3>
                                </div>
                                <div class="box-body pad">
                                    <textarea id="editor2" name="features_details" rows="10" cols="80" required>{{$policy->features_details}}</textarea>
                                    @if ($errors->has('features_details'))
                                        <div style="color: crimson;font-size: 18px;" class="error">{{ $errors->first('features_details') }}</div>
                                    @endif
                                </div>
                               
                            </div>
                    </div>

                    <!-- editor -->
                
                    
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary col-md-offset-4 col-md-4">تعديل</button>
                    </div>
    {!! Form::close() !!}
    </div>
</div> 
</div>
</section>

@endsection 