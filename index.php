<? require_once('Class_BD.php'); 
   
   $BD = new Class_BD();
   $Data = $BD->select('people', Array('*'), '', false, false); 
?>
<!DOCTYPE html>
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<title>База людей из города Кемерово</title>
		<meta name="description" content="Worthy a Bootstrap-based, Responsive HTML5 Template">
		<meta name="author" content="htmlcoder.me">

		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Favicon -->
		<link rel="shortcut icon" href="images/favicon.ico">

		<!-- Web Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700,300&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Raleway:700,400,300' rel='stylesheet' type='text/css'>

		<!-- Bootstrap core CSS -->
		<link href="bootstrap/css/bootstrap.css" rel="stylesheet">

		<!-- Font Awesome CSS -->
		<link href="fonts/font-awesome/css/font-awesome.css" rel="stylesheet">

		<!-- Plugins -->
		<link href="css/animations.css" rel="stylesheet">

		<!-- Worthy core CSS file -->
		<link href="css/style.css" rel="stylesheet">

		<!-- Custom css --> 
		<link href="css/custom.css" rel="stylesheet">
		
		<style>
		  .table{
            	border: 1px solid #eee;
	            table-layout: fixed;
	            width: 100%;
	            margin-bottom: 20px;
            }
          
		  .table th {
	            font-weight: bold;
	            padding: 5px;
	            background: #efefef;
	            border: 1px solid #dddddd;
            }
			
          .table td{
	            padding: 5px 10px;
	            border: 1px solid #eee;
	            text-align: left;
            }
			
          .table tbody tr:nth-child(odd){
	            background: #fff;
            }
			
          .table tbody tr:nth-child(even){
	            background: #F7F7F7;
            }
		</style>
		<style>
		  .custom-btn {
			  width: 130px;
			  height: 40px;
			  color: #fff;
			  border-radius: 5px;
			  padding: 10px 25px;
			  font-family: 'Lato', sans-serif;
			  font-weight: 500;
			  background: transparent;
			  cursor: pointer;
			  transition: all 0.3s ease;
			  position: relative;
			  display: inline-block;
			   box-shadow:inset 2px 2px 2px 0px rgba(255,255,255,.5),
			   7px 7px 20px 0px rgba(0,0,0,.1),
			   4px 4px 5px 0px rgba(0,0,0,.1);
			  outline: none;
			}
		
		 .btn-1 {
			  background: rgb(6,14,131);
			  background: linear-gradient(0deg, rgba(6,14,131,1) 0%, rgba(12,25,180,1) 100%);
			  border: none;
			}
		
		 .btn-1:hover {
			   background: rgb(0,3,255);
			background: linear-gradient(0deg, rgba(0,3,255,1) 0%, rgba(2,126,251,1) 100%);
			}	

          .disabled { 
		      display: none;
		  }

          .error {
              border: solid 2px red;
          }			  
		</style>
	</head>

	<body class="no-trans">
		<!-- scrollToTop -->
		<!-- ================ -->
		<div class="scrollToTop"><i class="icon-up-open-big"></i></div>

		<!-- header start -->
		<!-- ================ --> 
		<header class="header fixed clearfix navbar navbar-fixed-top">
			<div class="container">
				<div class="row">
					<div class="col-md-4">

						<!-- header-left start -->
						<!-- ================ -->
						<div class="header-left clearfix">

							<!-- logo -->
							<div class="logo smooth-scroll">
								<a href="#banner"><img id="logo" src="images/logo.png" alt="Worthy"></a>
							</div>

							<!-- name-and-slogan -->
							<div class="site-name-and-slogan smooth-scroll">
								<div class="site-name"><a href="#banner">База людей</a></div>
								<div class="site-slogan">из города <a target="_blank" href="">Кемерово</a></div>
							</div>

						</div>
						<!-- header-left end -->

					</div>
					<div class="col-md-8">

						<!-- header-right start -->
						<!-- ================ -->
						<div class="header-right clearfix">

							<!-- main-navigation start -->
							<!-- ================ -->
							<div class="main-navigation animated">

								<!-- navbar start -->
								<!-- ================ -->
								<nav class="navbar navbar-default" role="navigation">
									<div class="container-fluid">

										<!-- Toggle get grouped for better mobile display -->
										<div class="navbar-header">
											<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
												<span class="sr-only">Toggle navigation</span>
												<span class="icon-bar"></span>
												<span class="icon-bar"></span>
												<span class="icon-bar"></span>
											</button>
										</div>

										<!-- Collect the nav links, forms, and other content for toggling -->
										<div class="collapse navbar-collapse scrollspy smooth-scroll" id="navbar-collapse-1">
											<ul class="nav navbar-nav navbar-right">
												<li class="active"><a href="#banner">Главная</a></li>
												<li><a href="#contact">Контакты</a></li>
											</ul>
										</div>

									</div>
								</nav>
								<!-- navbar end -->

							</div>
							<!-- main-navigation end -->

						</div>
						<!-- header-right end -->

					</div>
				</div>
			</div>
		</header>
		<!-- header end -->

		<!-- banner start -->
		<!-- ================ -->
		<div id="banner" class="banner">
			<div class="banner-image"></div>
			<div class="banner-caption">
				<div class="container">
					<div class="row">
						<div class="col-md-8 col-md-offset-2 object-non-visible" data-animation-effect="fadeIn">
							<h1 class="text-center">База людей <span>Кемерово</span></h1>
							<p class="lead text-center">Рабочая база людей города Кемерово. Полный список ФИО людей проживающих в Кемерово. Редактор имен для людей из Кемерово.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- banner end -->

		<!-- section start -->
		<!-- ================ -->
		<div class="section clearfix object-non-visible" data-animation-effect="fadeIn">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1 id="about" class="title text-center">База людей <span>Кемерово</span></h1>
						<p class="lead text-center">Данные о людях проживающих в городе Кемерово.</p>
						<div class="space"></div>
						<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
						<script>
						    $(document).ready(function() {
								 var operation;
		                         function RefreshDBGrid() {
								     var table_id = $('#table_id');
									 
									 $.ajax({
                                            type: "POST",
                                            url: "view_record.php",
				                            data: "",
			                                success: function(data) {					                 
					                          console.log('success view data of table ');
					                          table_id.html(data);         
									        },                 
				                            error: function(data) {
					                           console.log('Error: '+ data);  
				                            }	  
				                           });	
								 }
								  $(document).on('click', '.checkbox', function() {                                  
									    var _this = $(this);
									    var checkbool = false;
										var paneledit = $("#paneledit");
									   	var famtext = $('#famtext'+_this.attr('id'));
                                        var famedittext = $('#famedittext'+_this.attr('id'));
                                            
									    var fnametext = $('#fnametext'+ _this.attr('id'));
                                        var fnameedittext = $('#fnameedittext'+ _this.attr('id'));

                                        var snametext = $('#snametext' + _this.attr('id'));
                                        var snameedittext = $('#snameedittext' + _this.attr('id'));
                                         
									    if (_this.is(':checked')) {
									       if (!famtext.hasClass('disabled')) 											  
											  famtext.addClass('disabled');

                                           if (famedittext.hasClass('disabled')) 
											  famedittext.removeClass('disabled');
                                            
										   if (!fnametext.hasClass('disabled')) 
											  fnametext.addClass('disabled');

                                            if (fnameedittext.hasClass('disabled')) 
											  fnameedittext.removeClass('disabled');

                                            if (!snametext.hasClass('disabled')) 
											  snametext.addClass('disabled');

                                            if (snameedittext.hasClass('disabled')) 
											  snameedittext.removeClass('disabled');
                                        
										} else {
                                            if (famtext.hasClass('disabled')) 
											  famtext.removeClass('disabled');
                                            
                                            if (!famedittext.hasClass('disabled')) 
											  famedittext.addClass('disabled');
										  
										    if (fnametext.hasClass('disabled')) 
											  fnametext.removeClass('disabled');
                                            
                                            if (!fnameedittext.hasClass('disabled')) 											  
											  fnameedittext.addClass('disabled');

                                            if (snametext.hasClass('disabled')) 
											  snametext.removeClass('disabled');

                                            if (!snameedittext.hasClass('disabled')) 
											  snameedittext.addClass('disabled');
                                        }
                                         
										$('.checkbox').each(function() { 
									       var t = $(this);
										   if (t.is(':checked')) {
										     checkbool = true;											
										   }	
									    });
                                        
										if (!checkbool)
										  if (!paneledit.hasClass('disabled'))
                                            paneledit.addClass('disabled');		  										
									}	
								  );

                                 $(document).on('click', '#canceledit', function() {									  
                                       var paneledit = $('#paneledit');	
                                       
                                       if (!paneledit.hasClass('disabled'))
                                         	paneledit.addClass('disabled');									   
									}	
								 );								  
								 
								 $(document).on('click', '#edit', function() {
								      var checks = $('.checkbox');
									  var check_elems = [];
									  var checkbool = false; 
									  var paneledit = $('#paneledit');
									  var paneladd = $('#paneladd');
									  
									  console.log('Edit data...');
									  operation = 'edit';
									  $(checks).each(function() { 
									     var _this = $(this);
										 if (_this.is(':checked')) {
											checkbool = true;											
										 }	 
									  });
									  if (checkbool) {
										if (!paneladd.hasClass('disabled'))
                                          paneladd.addClass('disabled');

                                        if (paneledit.hasClass('disabled'))
                                          paneledit.removeClass('disabled');
                                        else 
                                          paneledit.addClass('disabled');											
									  }	  
									  
                                    }										
								  );
								  
								  $(document).on('click', '#del', function() {
								      var checks = $('.checkbox');
									  var check_elems = [];
									  
									  console.log('Delete data...');
									   $(checks).each(function() {
							              var _this = $(this);
										  if (_this.is(':checked'))
											  check_elems.push(_this.attr('id'));
									   });
									   									  
									   $.ajax({
                                            type: "POST",
                                            url: "del_record.php",
				                            data: "data="+JSON.stringify(check_elems)+"",
			                                success: function(data) {					                 
					                          console.log('success delete data in table: '+data);
					                          RefreshDBGrid();
									        },                 
				                            error: function(data) {
					                           console.log('Error: '+ data);  
				                            }	  
				                        });	
								  });	
								 
								 $("#add").click(
                                   function(e) {	   
                                      var paneladd = $("#paneladd");									  
                                      var paneledit = $("#paneledit");
									  operation = "add";
									 
                                      if (!paneledit.hasClass('disabled'))
                                          paneledit.addClass('disabled');											
									   									  
									  if (paneladd.hasClass('disabled'))
										  paneladd.removeClass('disabled');
									  else
										  paneladd.addClass('disabled');								  
								   });
								   
								  $("#cancel").click(
								    function() { 
									  var paneladd = $("#paneladd");
									  if (!paneladd.hasClass('disabled'))
									    paneladd.addClass('disabled');								   	  
									}  
								  ); 
								 
								 $("#saveedit").click(                                   
								   function(e) {
									  var data_mas = [];
                                      $('.checkbox').each(function() { 
									       var _this = $(this);
										   if (_this.is(':checked')) {
										      var famedittext = $('#famedit'+_this.attr('id'));                                            
									          var fnameedittext = $('#fnameedit'+ _this.attr('id'));
                                              var snameedittext = $('#snameedit' + _this.attr('id'));	
                                                                                           											  
										      data_mas.push([famedittext.val(), fnameedittext.val(), snameedittext.val(), _this.attr('id')]);
										      //console.log(famedittext.attr('id')+":"+famval);
										   }	
									    });
										
										//console.log(data_mas);
										
										$.get("edit_record.php", 
					                      {mas: JSON.stringify(data_mas)}
						                ).done(
					                      function(data) {
                                             console.log( "Record success editing: " + data);
								             RefreshDBGrid();
                                        });
                                   }
                                 );								   
								   
								 $("#save").click(
								    function() {
									   var fam_input = $("#fam_input");
									   var name_input = $("#name_input");
									   var sname_input = $("#sname_input");
									   var val_fam = fam_input.val();
									   var val_name = name_input.val();
									   var val_sname = sname_input.val();
									   var error = false;
									   
									   if (val_fam == '') { 
										  if (!fam_input.hasClass('error'))
                                             fam_input.addClass('error');
										  error = true;
									   } else {
                                          if (fam_input.hasClass('error'))
                                             fam_input.removeClass('error'); 											  
                                       }

									   if (val_name == '') { 
										  if (!name_input.hasClass('error'))
                                             name_input.addClass('error');
										  error = true;
									   } else {
                                          if (name_input.hasClass('error'))
                                             name_input.removeClass('error'); 											  
                                       }
									   
									   if (val_sname == '') { 
										  if (!sname_input.hasClass('error'))
                                             sname_input.addClass('error');
										  error = true;
									   } else {
                                          if (sname_input.hasClass('error'))
                                             sname_input.removeClass('error'); 											  
                                       }
	                                   
									   if (!error) {
										   $.ajax({
                                            type: "POST",
                                            url: "add_record.php",
				                            data: "fam_input="+val_fam+"&name_input="+val_name+"&sname_input="+val_sname+"",
			                                success: function(data) {					                 
					                          console.log('success insert data in table: '+data);
					                          RefreshDBGrid();
									        },                 
				                            error: function(data) {
					                           console.log('Error: '+ data);  
				                            }	  
				                           });	
										   
									       $("#cancel").click();
										   fam_input.val("");
										   name_input.val("");
										   sname_input.val("");
                                       }									   
									}	
								  );
							}); 								   
						</script>
						<div class="row">	
                            <div style="padding-bottom: 20px;"> 						
						    	<button id="add" class="custom-btn btn-1">Добавить</button>
						        <button id="edit" class="custom-btn btn-1">Изменить</button>
						        <button id="del" class="custom-btn btn-1">Удалить</button>
							</div>
							<table class="table" id="table_id">
							<thead>
								<tr>
									<th>№</th>
									<th>Фамилия</th>
									<th>Имя</th>
									<th>Отчество</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<? foreach ($Data as $item) {?>
								<tr>
									<td><?=$item['id'];?></td>
									<td><p class="famtext" id="famtext<?=$item['id']?>"><?=$item['fam'];?></p>
									<span class="famedittext disabled" id="famedittext<?=$item['id'];?>"><input id="famedit<?=$item['id']?>" value="<?=$item['fam']?>"/></span></td>
									<td><p class="fnametext" id="fnametext<?=$item['id']?>"><?=$item['fname'];?></p>
									<span class="fnameedittext disabled" id="fnameedittext<?=$item['id'];?>"><input id="fnameedit<?=$item['id']?>" value="<?=$item['fname']?>"/></span></td>							
									<td><p class="snametext" id="snametext<?=$item['id']?>"><?=$item['sname'];?></p>
									<span class="snameedittext disabled" id="snameedittext<?=$item['id'];?>"><input id="snameedit<?=$item['id']?>" value="<?=$item['sname']?>"/></span></td>
							
									</td>
									<td><input type="checkbox" id="<?=$item["id"]?>" name="<?=$item['id'];?>" class="checkbox"></td>
								</tr>
								<? } ?>
							</tbody>
						</table>
						<div id="paneladd" class="disabled" style="padding: 20px;">
						  Фамилия:<input type="text" id="fam_input" name="fam_input" value=""/>
						  Имя:<input type="text" id="name_input" name="name_input" value=""/>
						  Отчество:<input type="text" id="sname_input" name="sname_input" value=""/><br>
						  <br>
						  <button id="save" class="custom-btn btn-1">Сохранить</button>
						  <button id="cancel" class="custom-btn btn-1 ">Отменить</button>
						</div>
						<div id="paneledit" class="disabled" style="padding: 20px;">
						  <button id="saveedit" class="custom-btn btn-1">Сохранить</button>
						  <button id="canceledit" class="custom-btn btn-1">Отменить</button>
						</div>
						</div>						
					</div>
				</div>
			</div>
		</div>
		<!-- section end -->

		
		<!-- footer start -->
		<!-- ================ -->
		<footer id="footer">

			<!-- .footer start -->
			<!-- ================ -->
			<div class="footer section">
				<div class="container">
					<h1 class="title text-center" id="contact">Контакты</h1>
					<div class="space"></div>
					<div class="row">
						<div class="col-sm-6">
							<div class="footer-content">
								<p class="large">Напишите нам сообщение, чтобы улучшить базу людей города Кемерово.</p>
								<ul class="list-icons">
									<li><i class="fa fa-map-marker pr-10"></i> Россия, город Кемерово</li>
									<li><i class="fa fa-phone pr-10"></i> +7-913-134-09-42</li>
									<li><i class="fa fa-fax pr-10"></i> +7-913-134-09-42 </li>
									<li><i class="fa fa-envelope-o pr-10"></i> integralal@mail.ru</li>
								</ul>
								<ul class="social-links">
									<li class="facebook"><a target="_blank" href="https://www.facebook.com/"><i class="fa fa-facebook"></i></a></li>
									<li class="twitter"><a target="_blank" href="https://twitter.com/"><i class="fa fa-twitter"></i></a></li>
									<li class="googleplus"><a target="_blank" href="http://plus.google.com"><i class="fa fa-google-plus"></i></a></li>
									<li class="skype"><a target="_blank" href="http://www.skype.com"><i class="fa fa-skype"></i></a></li>
									<li class="linkedin"><a target="_blank" href="http://www.linkedin.com"><i class="fa fa-linkedin"></i></a></li>
									<li class="youtube"><a target="_blank" href="http://www.youtube.com"><i class="fa fa-youtube"></i></a></li>
									<li class="flickr"><a target="_blank" href="http://www.flickr.com"><i class="fa fa-flickr"></i></a></li>
									<li class="pinterest"><a target="_blank" href="http://www.pinterest.com"><i class="fa fa-pinterest"></i></a></li>
								</ul>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="footer-content">
								<form role="form" id="footer-form">
									<div class="form-group has-feedback">
										<label class="sr-only" for="name2">Имя</label>
										<input type="text" class="form-control" id="name2" placeholder="Имя" name="name2" required>
										<i class="fa fa-user form-control-feedback"></i>
									</div>
									<div class="form-group has-feedback">
										<label class="sr-only" for="email2">Email</label>
										<input type="email" class="form-control" id="email2" placeholder="Введите email" name="email2" required>
										<i class="fa fa-envelope form-control-feedback"></i>
									</div>
									<div class="form-group has-feedback">
										<label class="sr-only" for="message2">Сообщение</label>
										<textarea class="form-control" rows="8" id="message2" placeholder="Сообщение" name="message2" required></textarea>
										<i class="fa fa-pencil form-control-feedback"></i>
									</div>
									<input type="submit" value="Отправить" class="btn btn-default">
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- .footer end -->

			<!-- .subfooter start -->
			<!-- ================ -->
			<div class="subfooter">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<p class="text-center">Copyright © 2022 IntegralAL <a target="_blank" href="mailto:integralal@mail.ru">IntegralAL@mail.ru</a>.</p>
						</div>
					</div>
				</div>
			</div>
			<!-- .subfooter end -->

		</footer>
		<!-- footer end -->

		<!-- JavaScript files placed at the end of the document so the pages load faster
		================================================== -->
		<!-- Jquery and Bootstap core js files -->
		<script type="text/javascript" src="plugins/jquery.min.js"></script>
		<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>

		<!-- Modernizr javascript -->
		<script type="text/javascript" src="plugins/modernizr.js"></script>

		<!-- Isotope javascript -->
		<script type="text/javascript" src="plugins/isotope/isotope.pkgd.min.js"></script>
		
		<!-- Backstretch javascript -->
		<script type="text/javascript" src="plugins/jquery.backstretch.min.js"></script>

		<!-- Appear javascript -->
		<script type="text/javascript" src="plugins/jquery.appear.js"></script>

		<!-- Initialization of Plugins -->
		<script type="text/javascript" src="js/template.js"></script>

		<!-- Custom Scripts -->
		<script type="text/javascript" src="js/custom.js"></script>

	</body>
</html>
