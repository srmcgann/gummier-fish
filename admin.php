<?php
	require("db.php");
	require("functions.php");
	require("login.php");
	if(!isset($_POST['maxResultsPerPage']))$_POST['maxResultsPerPage']=10;
	$maxResultsPerPage=$_POST['maxResultsPerPage'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="description" content="http://lookie.ml Admin Page">
		<meta name="keywords" content="share,images,videos">
		<link rel="stylesheet" type="text/css" href="admin.css">
		<link rel="shortcut icon" type="image/png" href="favicon.png"/>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
		<title>Lookie Admin Tool</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
		<script>
			$( function() { $( "#fromDate" ).datepicker(); } );
			$( function() { $( "#toDate" ).datepicker(); } );

			function resize(asset,type){
				a = $(asset)[0];
				switch(type){
					case "img":
						w=a.naturalWidth;
						h=a.naturalHeight;
						break;
					case "vid":
						w=a.videoWidth;
						h=a.videoHeight;
						break;
				}
				if(w > h){
					w2 = $(window).width()/2;
					h2 = Math.floor(h * (w2 / w));
				}else{
					h2 = $(window).height()/2;
					w2 = Math.floor(w * (h2 / h));			
				}
				$(asset).width(w2+"px");
				$(asset).height(h2+"px");
			}
			assets=new Array();
			visibilities=new Array();
			function resizeAll(){
				for(i=0;i<assets.length;++i){
					resize("#asset"+i,assets[i]);
				}
			}
		</script>
  </head>
	<body onresize="resizeAll()">
		<div id="loginDivOuter">
			<center>
				<div id="loginDivInner">
					<table style="float:left;">
						<tr><td>User Name</td><td><input type="text" id="preUserName"></td></tr>
						<tr><td>Password</td><td><input type="password" id="prePass"></td></tr>
						<script>
							$('#preUserName').keyup(function(e) {
								code = e.keyCode || e.which;
								if(code==13){
									$('#pass').val($('#prePass').val());
									$('#userName').val($('#preUserName').val());
									$('#selections').submit();
								}
								if(code==27){
									$('#loginDivOuter').hide();
								}
							});
							$('#prePass').keyup(function(e) {
								code = e.keyCode || e.which;
								if(code==13){
									$('#pass').val($('#prePass').val());
									$('#userName').val($('#preUserName').val());
									$('#selections').submit();
								}
								if(code==27){
									$('#loginDivOuter').hide();
								}
							});
						</script>
					</table>
					<button onclick="$('#pass').val($('#prePass').val());$('#userName').val($('#preUserName').val());$('#selections').submit();" style="width:100px;font-size:16px;">Login</button><br>
					<button style="margin-top:10px;width:100px;background:#66a;font-size:16px;" onclick="$('#loginDivOuter').hide();">Cancel</button>
					<div class="clear"></div>
				</div>
			</center>
		</div>
		<?php
			if($showAdminControls){
				?>
					<button id="logoutButton" onclick="$('#logout').val(1);$('#selections').submit();">Log Out</button>
				<?php
			}
		?>
		<div id="header">
			<a href="<?php echo $baseURL?>" style="color:#abc;text-decoration:none;	cursor: pointer;">
				LOOKIE.ml
				<br>
				<div style="font-size:16px;">
				<?php
					echo "&copy;".date("Y",strtotime("now")).' Scott McGann';
				?>
				</div>
			</a>
		</div>
		<div id="controlPanel">
			<center>
				<br>
				<button class="timeButtons" onclick="$('#fromDate').val('<?php echo date("m/d/Y",strtotime("now -1 day"))?>');$('#toDate').val('<?php echo date("m/d/Y",strtotime("now"))?>');$('#selections').submit();">Last Day</button>
				<button class="timeButtons" onclick="$('#fromDate').val('<?php echo date("m/d/Y",strtotime("now -1 week"))?>');$('#toDate').val('<?php echo date("m/d/Y",strtotime("now"))?>');$('#selections').submit();">Last Week</button>
				<button class="timeButtons" onclick="$('#fromDate').val('<?php echo date("m/d/Y",strtotime("now -1 month"))?>');$('#toDate').val('<?php echo date("m/d/Y",strtotime("now"))?>');$('#selections').submit();">Last Month</button>
				<button class="timeButtons" onclick="$('#fromDate').val('12/31/1969');$('#toDate').val('<?php echo date("m/d/Y",strtotime("now"))?>');$('#selections').submit();">All of Time</button>
				<form action="admin.php" method="post" id="selections">
					<table id="controlsTable">
						<tr><td style="text-align:right;">From Date:</td><td><input type="text" id="fromDate" name="fromDate" value="<?php echo $_POST['fromDate']?$_POST['fromDate']:date("m/d/Y",strtotime("epoch"));?>"></td></tr>
						<tr><td style="text-align:right;">To Date:</td><td><input type="text" id="toDate" name="toDate" value="<?php echo date("m/d/Y",strtotime("now"));?>"></td></tr>
						<tr><td style="text-align:right;">Sort:</td><td>
							<input type="radio" id="ascending" onchange="$('#selections').submit();" name="sort" value="ASC" <?php if($_POST['sort']=="ASC")echo "checked";?>>Ascending<br>
							<input type="radio" id="descending" onchange="$('#selections').submit();" name="sort" value="DESC" <?php if($_POST['sort']=="DESC")echo "checked";if(!isset($_POST['sort']))echo "checked";?>>Descending
						</td></tr>
						<tr><td style="text-align:right;">IP Address:</td><td><input type="text" id="IPAddress" name="IPAddress" onclick="$('#IPAddress').val('')" value="<?php echo isset($_POST['IPAddress'])?$_POST['IPAddress']:"*";?>" style="font-size:16px;height:18px;"><button type="submit" style="font-size:14px;">Go!</button></td></tr>
						<tr><td style="text-align:right;">Format:</td><td>
						<select id="format" name="format" onchange="$('#selections').submit();">
							<option value="*">*
							<option value="image/jpeg" <?php if($_POST['format']=="image/jpeg")echo "selected";?>>JPG
							<option value="image/png" <?php if($_POST['format']=="image/png")echo "selected";?>>PNG
							<option value="image/gif" <?php if($_POST['format']=="image/gif")echo "selected";?>>GIF
							<option value="image/bmp" <?php if($_POST['format']=="image/bmp")echo "selected";?>>BMP
							<option value="video/mp4" <?php if($_POST['format']=="video/mp4")echo "selected";?>>MP4
							<option value="video/webm" <?php if($_POST['format']=="video/webm")echo "selected";?>>WEBM
							<option value="video/ogg" <?php if($_POST['format']=="video/ogg")echo "selected";?>>OGG
						</select></td></tr>
						<tr><td style="text-align:right;">Results/Page:</td><td>
						<select id="maxResultsPerPage" name="maxResultsPerPage" onchange="$('#selections').submit();">
							<option value="1" <?php if($_POST['maxResultsPerPage']==1)echo "selected";?>><?echo 1?>
							<option value="5" <?php if($_POST['maxResultsPerPage']==5)echo "selected";?>><?php echo 5?>
							<option value="10" <?php if($_POST['maxResultsPerPage']==10)echo "selected";?>><?php echo 10?>
							<option value="25" <?php if($_POST['maxResultsPerPage']==25)echo "selected";?>><?php echo 25?>
							<option value="50" <?php if($_POST['maxResultsPerPage']==50)echo "selected";?>><?php echo 50?>
							<option value="100" <?php if($_POST['maxResultsPerPage']==100)echo "selected";?>><?php echo 100?>
							<option value="200" <?php if($_POST['maxResultsPerPage']==200)echo "selected";?>><?php echo 200?>
							<option value="500" <?php if($_POST['maxResultsPerPage']==500)echo "selected";?>><?php echo 500?>
						</select></td></tr>
					</table>
					<br>
					<input type="hidden" name="page" id="page" value="<?php echo $_POST['page']?$_POST['page']:0;?>">
					<input type="hidden" name="maxPage" id="maxPage">
					<input type="hidden" name="delete" id="delete">
					<input type="hidden" name="promote" id="promote">
					<input type="hidden" name="demote" id="demote">
					<input type="hidden" name="userName" id="userName">
					<input type="hidden" name="pass" id="pass">
					<input type="hidden" name="logout" id="logout">
				</form>
				<div id="pageDiv"></div>				
			</center>
		</div>
		<div id="masterContainer">
			<center>
				<span style="font-size:48px;font-style:italic;">LOOKIE ADMIN PANEL</span>
				<div id="mainDiv">
					<?php
						if($_POST['sort']){
							$fromDate=date("Y-m-d 00:00:00",strtotime($_POST['fromDate']));
							$toDate=date("Y-m-d 23:59:59",strtotime($_POST['toDate']));
							$sort=$_POST['sort'];
							$IPAddress=$_POST['IPAddress'];
							$format=$_POST['format'];
							$sql="SELECT * FROM images WHERE date BETWEEN '$fromDate' AND '$toDate'";
							if($IPAddress!="" && $IPAddress!="*") $sql.=" AND IP = \"$IPAddress\"";
							if($format!="*") $sql.=" AND type = \"$format\"";
							$sql.=" ORDER BY date $sort";
							$res=$link->query($sql);
							if(mysqli_num_rows($res)){
								$pages=ceil(mysqli_num_rows($res)/$maxResultsPerPage);
								if($_POST['page']>$pages){
									$_POST['page']=$pages-1;
									echo "<script>$('#page').val($pages-1);</script>";
								}
								$pageDiv="<table><tr><td>Total Results:</td><td>".number_format(mysqli_num_rows($res))."</td></tr>";
								$pageDiv.="<tr><td>Total Pages:</td><td>".number_format($pages)."</td></tr></table><br>";
								$pageDiv.="<div style='margin:20px;margin-top:0px;'>";
								if($_POST['page']>0){
									$pageDiv .= "<a style='float:left;' href=\\\"javascript:$('#page').val($('#page').val()-1);$('#selections').submit();\\\"><img class='arrow' src=\\\"left_arrow.png\\\"/></a>";
									$pageDiv .= "<div style='margin-top:0px;width:150px;float:left;margin-left:20px;font-size:28px;'> PAGE ";
									$pageDiv .= "<br><select style='width:150px;' id='pageSelect' onchange=\\\"$('#page').val($('#pageSelect').val());$('#selections').submit();\\\">";
									for($j=0;$j<$pages;++$j){
										$pageDiv .= "<option value='$j' ".($j==$_POST['page']?"selected":"").">".($j+1);
									}
									$pageDiv .= "</select></div>";
								}else{
									$pageDiv .= "<img class='arrow' style='float:left;' src=\\\"left_arrow_disabled.png\\\"/>";
									$pageDiv .= "<div style='margin-top:0px;width:150px;float:left;margin-left:20px;font-size:28px;'> PAGE ";
									$pageDiv .= "<br><select style='width:150px;' id='pageSelect' onchange=\\\"$('#page').val($('#pageSelect').val());$('#selections').submit();\\\">";
									for($j=0;$j<$pages;++$j){
										$pageDiv .= "<option value='$j' ".($j==$_POST['page']?"selected":"").">".($j+1);
									}
									$pageDiv .= "</select></div>";
								}
								if($_POST['page']<$pages-1){
									$pageDiv.="<a href=\\\"javascript:$('#page').val(parseInt($('#page').val())+1);$('#selections').submit();\\\"><img class='arrow' style='float:right;' src=\\\"right_arrow.png\\\"/></a>";
								}else{
									$pageDiv.="<img class='arrow' style='float:right;' src=\\\"right_arrow_disabled.png\\\"/>";
								}
								$pageDiv.="<div class='clear'></div>";
								$pageDiv.="</div>";
								echo '<script>$("#pageDiv").html("'.$pageDiv.'")</script>';

								for($i=0;$i<$_POST['page']*$maxResultsPerPage;++$i)$row=mysqli_fetch_assoc($res);
								for($i=$_POST['page']*$maxResultsPerPage;$i<(mysqli_num_rows($res)<($_POST['page']+1)*$maxResultsPerPage?mysqli_num_rows($res):($_POST['page']+1)*$maxResultsPerPage);++$i){
									$row=mysqli_fetch_assoc($res);
									$shortName=$row['shortName'];
									$name=$row['name'];
									$IP=$row['IP'];
									$date=$row['date'];
									$shortName=$row['shortName'];
									$views=$row['views'];
									$type=$row['type'];
									$public=$row['public'];
									?><div class="assetItem"><?php
									switch($type){
										case "image/jpeg":
											echo "<img src=\"uploads/$shortName.jpg\" id=\"asset$i\"/>";
											echo '<script>$("#asset'.$i.'").load(resize("#asset'.$i.'","img"));assets.push("img");</script>';
											break;
										case "image/png":
											echo "<img src=\"uploads/$shortName.png\" id=\"asset$i\"/>";
											echo '<script>$("#asset'.$i.'").load(resize("#asset'.$i.'","img"));assets.push("img");</script>';
											break;
										case "image/gif":
											echo "<img src=\"uploads/$shortName.gif\" id=\"asset$i\"/>";
											echo '<script>$("#asset'.$i.'").load(resize("#asset'.$i.'","img"));assets.push("img");</script>';
											break;
										case "image/bmp":
											echo "<img src=\"uploads/$shortName.bmp\" id=\"asset$i\"/>";
											echo '<script>$("#asset'.$i.'").load(resize("#asset'.$i.'","img"));assets.push("img");</script>';
											break;
											break;
										case "video/mp4":
											echo "<video controls loop src=\"uploads/$shortName.mp4\" id=\"asset$i\"/>Your browser does not support the video tag.</video>";
											echo '<script>$("#asset'.$i.'").bind("loadedmetadata", resize("#asset'.$i.'","vid"));assets.push("vid");</script>';
											break;
										case "video/webm":
											echo "<video controls loop src=\"uploads/$shortName.webm\" id=\"asset$i\"/>Your browser does not support the video tag.</video>";
											echo '<script>$("#asset'.$i.'").bind("loadedmetadata", resize("#asset'.$i.'","vid"));assets.push("vid");</script>';
											break;
										case "video/ogg":
											echo "<video controls loop src=\"uploads/$shortName.ogg\" id=\"asset$i\"/>Your browser does not support the video tag.</video>";
											echo '<script>$("#asset'.$i.'").bind("loadedmetadata", resize("#asset'.$i.'","vid"));assets.push("vid");</script>';
											break;
									}
									?>
										<center>
											<div class="fileInfoDiv">
												<table class="fileInfo">
													<tr>
														<td style="color:#fff;font-weight:300;text-align:center;width:150px;">Name: </td>
														<td><?php echo strlen($name)<32?$name:substr($name,0,32)."...";?></td>
													</tr>
													<tr>
														<td style="color:#fff;font-weight:300;text-align:center;width:150px;">Views: </td>
														<td><?php echo number_format($views);?></td>
													</tr>
													<tr>
														<td style="color:#fff;font-weight:300;text-align:center;width:150px;">Type: </td>
														<td><?php echo $type;?></td>
													</tr>
													<tr>
														<td style="color:#fff;font-weight:300;text-align:center;width:150px;">IP Address: </td>
														<td><?php echo $IP;?></td>
													</tr>
													<tr>
														<td style="color:#fff;font-weight:300;text-align:center;width:150px;">Date & Time: </td>
														<td><?php echo $date;?></td>
													</tr>
													<tr>
														<td style="color:#fff;font-weight:300;text-align:center;width:150px;">URL: </td>
														<td><a href="<?php echo "$baseURL?i=$shortName"?>" target="_blank"><?php echo "$baseURL?i=$shortName"?></a></td>
													</tr>
													<tr>
														<td style="color:#fff;font-weight:300;text-align:center;width:150px;">Gallery:</td>
														<td id="galleryVisible<?php echo $i?>"><?php echo $public?"Visible":"Hidden";?></td>
													</tr>
												</table>
												<?php  if($showAdminControls){ ?>
													<div id="visDiv<?php echo $i?>">
														<?php 
															if($public){
																?>
																	<div id="visStatus<?php echo $i?>"> This asset is public.</div>
																	<button class="publicButton" id="visButton<?php echo $i;?>">Make Private</button>
																	<script>visibilities.push(1);</script>
																<?php 
															}else{
																?>
																	<div id="visStatus<?php echo $i?>"> This asset is private.</div>
																	<button class="privateButton" id="visButton<?php echo $i;?>">Make Public</button>
																	<script>visibilities.push(0);</script>
																<?php 
															}
														?>
														<br><hr><br>
														<button class="deleteButton" id="deleteButton<?php echo $i?>">Delete Asset</button>
													</div>
													<script>
														$("#visButton<?php echo $i?>").click(function(){
															visibilities[<?php echo $i?>]=visibilities[<?php echo $i?>]?0:1;
															$.post("toggleVis.php",
															{
																shortName: "<?php echo $shortName?>",
																pub: visibilities[<?php echo $i?>]
															},
															function(data){
																if(data=="0"){
																	$( "#visButton<?php echo $i?>" ).removeClass( "publicButton" ).addClass( "privateButton" );
																	$( "#visButton<?php echo $i?>" ).html( "Make Public" );
																	$( "#visStatus<?php echo $i?>" ).html( "This asset is private." );
																	$( "#galleryVisible<?php echo $i?>" ).html( "Hidden." );
																}else{
																	$( "#visButton<?php echo $i?>" ).removeClass( "privateButton" ).addClass( "publicButton" );
																	$( "#visButton<?php echo $i?>" ).html( "Make Private" );
																	$( "#visStatus<?php echo $i?>" ).html( "This asset is public." );
																	$( "#galleryVisible<?php echo $i?>" ).html( "Visible." );
																}
															});
														});
														$("#deleteButton<?php echo $i?>").click(function(){
															if(confirm("Are you sure?!?\n\nThis action cannot be undone!")){															
																$.post("deleteAsset.php",
																{
																	shortName: "<?php echo $shortName?>"
																},
																function(data){
																	document.location.reload();
																});
															}
														});
													</script>
												<?php }else{ ?>
													<br>You must login to<br>access admin controls!<br><br>
													<button class="loginButton" onclick="$('#loginDivOuter').show();$('#preUserName').focus();">Login</button>
												<?php } ?>
												<div class="clear"></div>
											</div>
										</center>
										</div>
									<?php
								}
							}else{
								?>
									<div style="margin-top:25%;font-size:28px;">
										No Results.<br><br><br>
										<span style="font-size:16px;"><?php echo $sql;?></span>
									</div>
								<?php
							}
						}else{
							?>
							<script>
								$("#selections").submit();
							</script>
							<?php
						}
					?>
				</div>
			</center>
		</div>
		<?php
			if($loginError!=""){
				?>
					<script>
						alert("<?php echo $loginError;?>");
					</script>
				<?php
			}
		?>
	</body>
</html>