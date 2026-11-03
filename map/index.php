<?php		 
$dbserver = '192.168.2.254';
$dbuser = 'opd' ; 
$dbpass= "opd";
$dbname= 'hos';
error_reporting(E_ALL ^ E_NOTICE);
$con=mysqli_connect($dbserver, $dbuser, $dbpass) or die("เชื่อมต่อฐานข้อมูลไม่ได้ ");
mysqli_select_db($con,$dbname) or die("เลือกฐานข้อมูลไม่ได้"); // เลือกฐานข้อมูล
mysqli_query($con,"SET NAMES UTF8");
if (!$_GET['xxx'])  //ถ้ามีี $get xxx เข้ามาไม่ต้องรีโหลด ดังนั้นเราจะทำการโหลดได้แค่ครั้งเดียว
{
//echo "<meta http-equiv='refresh' content='0;URL=index.php?xxx=1'>";
}
?>

<style>
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td {
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-size: 100%;
	vertical-align: baseline;
	background: transparent;
}
.img_left{ float:left; margin-right:5px; margin-bottom:5px; border:1px dotted #999999; background-color:#f2f2f2; padding:2px;}
.cls{ clear:both;}
.font_map{ font-family:Tahoma, Arial, serif; font-size:13px;}
div#map_canvas { content: width:80%; height:100%; }
</style>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbUirp-8aEH-i9K3Zxn4WCzPSvURlV1bc&callback=map&sensor=false" type="text/javascript"></script>
<script type="text/javascript" src="http://www.cyberthai.net/gmap3.js"></script> 

 <script type="text/javascript">

        $(function () {
            $('#map_canvas').gmap3({
                map: {
                    options: {
                        center: [17.850128, 103.569960],
                        zoom: 15,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        mapTypeControl: true,
                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                        },
                    }
                },
                marker: {
                    values: [
<?php
$sql = mysqli_query($con,"SELECT MAX(ov.vstdate) AS vstdate,pp.`name`,ov.hn,pt.cid,CONCAT(pt.pname,pt.fname,' ',pt.lname) AS ptname,pt.birthday,(YEAR(NOW()) -YEAR(pt.birthday)) AS age
,CONCAT(pt.addrpart,' หมู่ ',pt.moopart,' ',th.full_name) AS addr,zn.rpst_name,zn.rpst_id
,(SELECT vn.pdx FROM vn_stat vn WHERE vn.vn=ov.vn) AS C 
,if(ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515','Z515',NULL) AS Z 
,if(ISNULL(pt.death) OR pt.death='','N',pt.death) AS death
,(SELECT COUNT(*) AS dayc FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn=a1.vn WHERE vn.hn=ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103) AS dayc
,DATEDIFF(NOW(),(SELECT MAX(a1.entry_datetime)FROM ovst_community_service a1 INNER JOIN vn_stat vn ON vn.vn=a1.vn WHERE vn.hn=ov.hn AND a1.ovst_community_service_type_id BETWEEN 1 AND 103)) AS daym,h.latitude,h.longitude 
FROM vn_stat ov 
LEFT JOIN pttype pp ON pp.pttype=ov.pttype
LEFT JOIN ovst_community_service oc ON oc.vn=ov.vn AND oc.ovst_community_service_type_id BETWEEN 1 AND 103
LEFT JOIN patient pt ON pt.hn=ov.hn
LEFT JOIN thaiaddress th ON th.addressid = CONCAT(pt.chwpart,pt.amppart,pt.tmbpart)
LEFT JOIN zbm_rpst zr ON zr.chwpart=pt.chwpart AND zr.amppart=pt.amppart AND zr.tmbpart=pt.tmbpart AND zr.moopart=pt.moopart
LEFT JOIN zbm_rpst_name zn ON zn.rpst_id=zr.rpst_id
LEFT JOIN person p on p.cid=pt.cid
LEFT JOIN house h on h.house_id=p.house_id
WHERE (ov.pdx ='Z515' OR ov.dx0 ='Z515' OR ov.dx1 ='Z515' OR ov.dx2 ='Z515' OR ov.dx3 ='Z515' OR ov.dx4 ='Z515' OR ov.dx5 ='Z515')
AND ov.vstdate BETWEEN '2018-10-01' AND '2019-10-31' GROUP BY ov.hn ORDER BY ov.vstdate DESC
");
$num = mysqli_num_rows($sql);
if ($num>0){
	while ($r=mysqli_fetch_array($sql))	{
		++$i;
		$i != $num ? $k=',' : $k='';
?>
{latLng:[<?php echo $r[latitude]?>, <?php echo $r[longitude]?>], data:"<div class='font_map'><img src='<?php echo $r[Pic]?>' width='75' height='75' alt='<?php echo $r[census_id]?>' class='img_left' /><strong><a href='#' title='<?php echo $r[census_id]?>' target='_blank'><?php echo $r[ptname]?></a></strong><br /><br /><b>ที่อยู่ :</b></font><?php echo $r[addr]?><br /><b>รหัสโรค :</b><?php echo $r[C]?><div class='cls'></div><a href='#' title='<?php echo $r[census_id]?>' target='_blank'>ดูที่เหลือ</a></div>", options:{icon: "https://icon-icons.com/icons2/1749/PNG/64/06_113688.png"}}<?php echo $k?>
<?php
	}
}
?>

                    ],
                    events: {
                        mouseover: function (marker, event, context) {
                            var map = $(this).gmap3("get"),
                                infowindow = $(this).gmap3({
                                    get: {
                                        name: "infowindow"
                                    }
                                });
                            if (infowindow) {
                                infowindow.open(map, marker);
                                infowindow.setContent(context.data);
                            } else {
                                $(this).gmap3({
                                    infowindow: {
                                        anchor: marker,
                                        options: {
                                            content: context.data
                                        }
                                    }
                                });
                            }
                        },
                        closeclick: function () {
                            infowindow.close();
                        },
                        mouseout: function () {
                            var infowindow = $(this).gmap3({
                                get: {
                                    name: "infowindow"
                                }
                            });
                        }
                    }
                }
            });
        });
		
    </script>
    
<div id="map_canvas"></div>
