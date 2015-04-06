
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <title>Custom menu in Gantt Chart</title>
    </head>
    <body onload="createChartControl('GanttDiv')">        
    <link type="text/css" rel="stylesheet" href="../assets/gantt/dhtmlxGantt/codebase/dhtmlxgantt.css">
    <script type="text/javascript" language="JavaScript" src="../assets/gantt/dhtmlxGantt/codebase/dhtmlxcommon.js"></script>
    <script type="text/javascript" language="JavaScript" src="../assets/gantt/dhtmlxGantt/codebase/dhtmlxgantt.js"></script>

    <link rel="stylesheet" type="text/css" href="../assets/gantt/dhtmlxMenu/codebase/skins/dhtmlxmenu_dhx_skyblue.css">
    <script type="text/javascript" language="JavaScript" src="../assets/gantt/dhtmlxMenu/codebase/dhtmlxmenu.js"></script>
    <script type="text/javascript" language="JavaScript" src="../assets/gantt/dhtmlxMenu/codebase/ext/dhtmlxmenu_ext.js"></script>
    <script type="text/javascript" language="JavaScript" src="../assets/js/jquery-1.9.1.min.js"></script>

    <script type="text/javascript" language="JavaScript">
    
        var x = 1;
        // var aaa = '<?php echo "project"."'+x+'";?>';
        // console.log(aaa);
        // var_dump($ax);
        // echo $ax; 
        
       
        // var project2 = new GanttProjectInfo(1, "Jembatan", new Date(2010, 5, 11));
        // var ins=0;
        // var wkt = 120;       
        // var myCars=[10,15];
    var ganttChartControl;
    
    // function daterange(id,tgl_awal,tgl_akhir){
    //     var start = new Date(tgl_awal);
    //     var end = new Date(tgl_akhir);

    //     while(start <= end){        
    //         console.log(id+start);
    //         var newDate = start.setDate(start.getDate() + 1);
    //         start = new Date(newDate);
    //     }
    // }

    function createChartControl(htmlDiv1)
    {
        // Initialize Gantt data structures
        
    var id;
    var uraian;
    var unit;
    var tgl_awal;
    var tgl_akhir;
    var bobot;
    var selisih;
    var tgl_awal_day;
    var tgl_awal_month;
    var tgl_awal_year;
    var p = new Array();
        $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>pengendalian/getsch2',
        data: { patientID: "1" },
        async: false,
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
         success: function(jsonData) {
            for(i=0;i<jsonData.data.length;i++){
                a = i +1;
            id = jsonData.data[i].id;
            uraian = jsonData.data[i].uraian;
            unit = jsonData.data[i].unit;
            tgl_awal = jsonData.data[i].tgl_awal;
            tgl_akhir = jsonData.data[i].tgl_akhir;
            bobot = parseInt(jsonData.data[i].bobot);
            selisih = parseInt(jsonData.data[i].selisih) +1;
            tgl_awal_day = parseInt(jsonData.data[i].tgl_awal_day);
            tgl_awal_month = parseInt(jsonData.data[i].tgl_awal_month);
            tgl_awal_year = parseInt(jsonData.data[i].tgl_awal_year);
            // console.log(selisih);
            // console.log('<?php echo "project"."'+a+'";?>');
            
            p[i]= new GanttProjectInfo(1, id+"."+uraian, new Date(tgl_awal_year, tgl_awal_month, tgl_awal_day));
            var parentTask1 = new GanttTaskInfo(1, tgl_awal+" => "+tgl_akhir, new Date(tgl_awal_year, tgl_awal_month, tgl_awal_day), (selisih)*24, bobot, ""); 
            parentTask1.addChildTask(new GanttTaskInfo(1, "Progress", new Date(tgl_awal_year, tgl_awal_month, tgl_awal_day), (selisih)*24, bobot, ""));
            p[i].addTask(parentTask1);

            // daterange(id,tgl_awal,tgl_akhir);
            
            // console.log(p[i]);
            // var prj = '<?php echo "project"."'+a+'";?>';
            // console.log(prj);
            // alert(id+uraian+unit+tgl_awal+tgl_akhir+bobot);
            }
         // alert(jsonData.data[0].id);
         },
         error: function() {
           alert('Error loading...');
         }
        });

        

        // var prj1 = new GanttProjectInfo(1, "Jembatan", new Date(2010, 5, 11));        
        //header...=>(no, uraian, tgl, jam, persen)
        // var parentTask1 = new GanttTaskInfo(1, "Juli 2013", new Date(2010, 5, 11), 120, 0, ""); 

        // prj1.addTask(parentTask1);
        // project1.addTask(parentTask2);
        // project2.addTask(parentTask1);
        // project2.addTask(parentTask2);
        // Create Gantt control
        ganttChartControl = new GanttChart();
        // Setup paths and behavior
        ganttChartControl.setImagePath("../assets/gantt/dhtmlxGantt/codebase/imgs/");
        ganttChartControl.setEditable(true);
        ganttChartControl.showTreePanel(true);
        ganttChartControl.showDescTask(true,'s-f');



        // Sample custom menu
        var menu = new dhtmlXMenuObject();
        menu.setIconsPath("../assets/gantt/dhtmlxMenu/codebase/imgs/dhtmlxmenu_dhx_skyblue");
        menu.renderAsContextMenu();
        menu.loadXMLString('<menu><item id="m1" text="Item name placeholder"/><item id="m2" text="Get info"/><item id="m3" text="Delete Task"/></menu>');
        menu.attachEvent("onClick", function(id){
            var obj = menu.getUserData("","obj");
            if (obj) {
                var o = obj.o;
                if (obj.type == "p") {
                    alert("Project, id=" + o.getId() + ", name=" + o.getName() + ", start date=" + o.getStartDate() +
                            ", duration=" + o.getDuration() + "hours, percent complete=" + o.getPercentCompleted() + "%");
                } else
                if (obj.type == "t") {
                    if(id=="m3") {
                        // Handle "Delete Task" menu
                        if (confirm("Delete task \""+o.getName()+"\"?")) ganttChartControl.getProjectById(1).deleteTask(o.getId());
                    } else
                        alert("Task, id=" + obj.o.getId() + ", name=" + obj.o.getName() + ", EST=" + obj.o.getEST() +
                              ", duration=" + o.getDuration() + "hours, percent complete=" + o.getPercentCompleted() + "%" +
                              ", parentTaskId=" + o.getParentTaskId() + ", pred.taskId=" + o.getPredecessorTaskId());
                }
            }
        });
        ganttChartControl.setContextMenu(menu);
        ganttChartControl.attachEvent("onBeforeContextMenu", function(menu,obj) {
            // Sample of disabling menu for a particular item with Id = 13
            if (obj.getId()==13) {alert("This task has no menu."); return false;}
            if (obj.isProject) {
                // Project menu
                menu.setItemText("m1", "Project: " + obj.getName());
                menu.setUserData("","obj",{type:"p",o:obj});
                menu.setItemDisabled("m3");
            } else if (obj.isTask) {
                // Task menu
                menu.setItemText("m1", "Task: " + obj.getName());
                menu.setUserData("","obj",{type:"t",o:obj});
                menu.setItemEnabled("m3");
            }
        });



        // console.log(p.length);
        // Load data structure     
        for (var addp = 0; addp < p.length; addp++) {
             ganttChartControl.addProject(p[addp]);
         }; 
        // ganttChartControl.addProject(prj1);
        // ganttChartControl.addProject(project2);
        // Build control on the page
        ganttChartControl.create(htmlDiv1);
    }
    /*]]>*/
</script>
<div style="width:'60%'; height:210px;" id="GanttDiv"></div>
<div id="content"> </div>
      </body>
</html>