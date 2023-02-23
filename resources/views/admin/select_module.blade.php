<!DOCTYPE html>

<html lang="en">


<head>

    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS only -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/3.1.1/js/jquery.orgchart.min.js" integrity="sha512-alnBKIRc2t6LkXj07dy2CLCByKoMYf2eQ5hLpDmjoqO44d3JF8LSM4PptrgvohTQT0LzKdRasI/wgLN0ONNgmA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>EduSmart - Tổ chức giáo dục GP School</title>

    <style type="text/css">
        .genealogy-scroll::-webkit-scrollbar {
            width: 5px;
            height: 8px;
        }
        .genealogy-scroll::-webkit-scrollbar-track {
            border-radius: 10px;
            background-color: #e4e4e4;
        }
        .genealogy-scroll::-webkit-scrollbar-thumb {
            background: #212121;
            border-radius: 10px;
            transition: 0.5s;
        }
        .genealogy-scroll::-webkit-scrollbar-thumb:hover {
            background: #d5b14c;
            transition: 0.5s;
        }


        /*----------------genealogy-tree----------*/
        .genealogy-body{
            white-space: wrap;
            overflow-y: hidden;
            padding: 50px;
            min-height: 500px;
            padding-top: 10px;
            margin:auto;
            text-align: center;
        }
        .genealogy-tree ul {
            padding-top: 20px; 
            position: relative;
            padding-left: 0px;
            display: flex;
        }
        .genealogy-tree li {
            float: left; text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 5px 0 5px;
        }
        .genealogy-tree li::before, .genealogy-tree li::after{
            content: '';
            position: absolute; 
          top: 0; 
          right: 50%;
            border-top: 2px solid #ccc;
            width: 50%; 
          height: 18px;
        }
        .genealogy-tree li::after{
            right: auto; left: 50%;
            border-left: 2px solid #ccc;
        }
        .genealogy-tree li:only-child::after, .genealogy-tree li:only-child::before {
            display: none;
        }
        .genealogy-tree li:only-child{ 
            padding-top: 0;
        }
        .genealogy-tree li:first-child::before, .genealogy-tree li:last-child::after{
            border: 0 none;
        }
        .genealogy-tree li:last-child::before{
            border-right: 2px solid #ccc;
            border-radius: 0 5px 0 0;
            -webkit-border-radius: 0 5px 0 0;
            -moz-border-radius: 0 5px 0 0;
        }
        .genealogy-tree li:first-child::after{
            border-radius: 5px 0 0 0;
            -webkit-border-radius: 5px 0 0 0;
            -moz-border-radius: 5px 0 0 0;
        }
        .genealogy-tree ul ul::before{
            content: '';
            position: absolute; top: 0; left: 50%;
            border-left: 2px solid #ccc;
            width: 0; height: 20px;
        }
        .genealogy-tree li a{
            text-decoration: none;
            color: #666;
            font-family: arial, verdana, tahoma;
            font-size: 11px;
            display: inline-block;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
        }

        .genealogy-tree li a:hover+ul li::after, 
        .genealogy-tree li a:hover+ul li::before, 
        .genealogy-tree li a:hover+ul::before, 
        .genealogy-tree li a:hover+ul ul::before{
            border-color:  #fbba00;
        }

        /*--------------memeber-card-design----------*/
        .member-view-box{
            text-align: center;
            border-radius: 4px;
            position: relative;
        }

        .member-image-logo{
          width: 200px;
            position: relative;
        }

        .member-image{
            width: 150px;
            position: relative;
        }
        .member-image img{
            width: 150px;
            height: auto;
            border-radius: 6px;
          background-color :#fff;
          z-index: 1;
        }

        
    </style>

</head>


<body>

<div class="body genealogy-body genealogy-scroll">
    <div class="genealogy-tree">
      <ul>
          <li>
            <a href="javascript:void(0);">
                <div class="member-view-box">
                    <div class="member-image-logo">
                        <img src="https://mamnon.edusmart.net.vn/themes/images/logo.png" alt="Member">
                        <div class="member-details">
                            <h5>Edusmart</5>
                        </div>
                    </div>
                </div>
            
            </a>
            <ul class="active">
              @foreach(SYSTEM_MODULES as $index => $module)
                  <li>
                      <a href="javascript:void(0);">
                          <div class="member-view-box">
                              <div class="member-image">
                                  <img src="https://mamnon.edusmart.net.vn/themes/images/logo.png" alt="Member">
                                  <div class="member-details">
                                      <span class="{{$module['isActive'] ? 'btn-success' : 'btn-danger'}}">{{$module['isActive'] ? 'Đang kích hoạt' : 'Chưa kích hoạt'}}</span>
                                      <h4>{{$module['name']}}</h4>
                                  </div>
                              </div>
                          </div>
                        </a>
                        <ul class="{{ in_array($index, [3]) ? 'active' : '' }}">
                            @foreach($module['functions'] as $function => $status)
                              <li>
                                  <a href="javascript:void(0);">
                                      <div class="member-view-box">
                                          <div >
                                            <a readonly href="{{ 'active' == $status ? route('admin.select_module', ['select_module' => $module['value']]) : '#'}}">
                                              <div class="member-details">
                                                  <span class="{{ 'active' == $status ? 'btn-success' : 'btn-danger'}}">{{'active' == $status ? 'Đang kích hoạt' : 'Chưa kích hoạt'}}</span>
                                                  <h5>{{ $function }}</5>
                                              </div>
                                            </a>
                                          </div>
                                      </div>
                                  </a>
                              </li>
                            @endforeach
                        </ul>
                    </li>
              @endforeach
            </ul>
          </li>
      </ul>
    </div>

   
</div>
    
<script>
  $(function () {
      $('.genealogy-tree ul').hide();
      $('.genealogy-tree>ul').show();
      $('.genealogy-tree ul.active').show();
      $('.genealogy-tree li').on('click', function (e) {
          var children = $(this).find('> ul');
          if (children.is(":visible")) children.hide('fast').removeClass('active');
          else children.show('fast').addClass('active');
          e.stopPropagation();
      });
  });

</script>

</body>

</html>