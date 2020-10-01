@extends('admin/include/master')
@section('title') لوحة التحكم | مشاهدة تفاصيل الطلب  @endsection
@section('content')

  <section class="content-header"></section>
    <section class="invoice">
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <i class="fa fa-globe"></i> كود الطلب   {{$showorder->invite_code}}#
            <small class="pull-left">تاريخ الطلب : {{ date('Y/m/d', strtotime($showorder->created_at)) }}</small>
          </h2>
        </div>
      </div>
     
      <div class="row invoice-info">
        <div class="col-sm-12 invoice-col">
            @if($showorder->status == 0)
                <span style="border-radius: 3px;border: 1px solid green;color: orange;float:left;padding: 3px;font-weight: bold;background: #fff;display: inline-block;margin-top: 4%;" class="ads__item__featured">قيد الانتظار</span>
            @elseif($showorder->status == 1) 
                  <span style="border-radius: 3px;border: 1px solid green;color: springgreen;float:left;padding: 3px;font-weight: bold;background: #fff;display: inline-block;margin-top: 4%;" class="ads__item__featured">مقبول</span>
            @elseif($showorder->status == 2)   
                  <span style="border-radius: 3px;border: 1px solid #c22356;float:left;color:crimson;padding: 3px;font-weight: bold;background: #fff;display: inline-block;margin-top: 4%;" class="ads__item__featured">مرفوض</span>
            
            @endif    
               
           
            
            
          صاحب الطلب
          <address>
           <a href="{{asset('adminpanel/users/'.$ownerinfo->id)}}"> 
            <strong>{{$showorder->name}}</strong> </a> <br>
             الهوية : {{$showorder->identity}}<br>
             رقم الجوال : {{$showorder->phone}}<br>
            العنوان     : {{$showorder->address}}<br> 
          </address>
        </div>
        
      <div class="row">
        <div class="col-xs-12">
          <div class="table-responsive">
            
              <?php 
                  $iteminfo  = DB::table('items')->where('id',$showorder->item_id)->first();
                  $itemimage = DB::table('item_images')->where('item_id',$item->item_id)->value('image');
              ?>
              <div class="col-md-8">
                <table class="table">
                    <tbody>

                      <tr>
                          <th style="width: 25%;"> عنصر الطلب</th>
                          
                          <td>
                            <a href="{{asset('adminpanel/items/'.$iteminfo->id)}}">{{$iteminfo->artitle}}</a>
                          </td>
                      </tr>
                      
                     
                      
                        
                    </tbody>
                </table>
              </div>
              <div class="col-md-4">
                  <img style="width:100%;height:110px;" src="{{asset('users/images/'.$itemimage)}}" alt="{{$iteminfo->artitle}}">
              </div>
            @endforeach
          </div>
          
        </div>
      </div>

    </section>
@endsection