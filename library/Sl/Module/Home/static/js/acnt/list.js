$(function () {

$(tree_selector)
    
    .jstree({ 
        // List of active plugins
        "plugins" : [ 
            "themes","json_data","ui","crrm","cookies","dnd","search","types","hotkeys" ,"contextmenu" 
        ], 
		 "themes" : {
            "theme" : "default",
            "dots" : true,
            "icons" : true,
            "url" : tree_theme_url,
        },

        // I usually configure the plugin that handles the data first
        // This example uses JSON as it is most common
        "json_data" : { 
            // This tree is ajax enabled - as this is most common, and maybe a bit more complex
            // All the options are almost the same as jQuery's AJAX (read the docs)
            "ajax" : {
                // the URL to fetch the data
                "url" : listtreeaction,
                // the `data` function is executed in the instance's scope
                // the parameter is the node being loaded 
                // (may be -1, 0, or undefined when loading the root nodes)
                "data" : function (n) { 
                    // the result is fed to the AJAX request `data` option
                    return { 
                        "operation" : "get_children", 
                        "relation" : tree_relation,
                        "id" : n.attr ? n.attr("id").replace("node_","") : -1 
                    }; 
                }
            }
        },
        // Configuring the search plugin
        "search" : {
            // As this has been a common question - async search
            // Same as above - the `ajax` config option is actually jQuery's AJAX object
            "ajax" : {
                "url" : listtreeaction,
                // You get the search string as a parameter
                "data" : function (str) {
                    return { 
                        "operation" : "search", 
                        "search_str" : str 
                    }; 
                }
            }
        },
        // Using types - most of the time this is an overkill
        // read the docs carefully to decide whether you need types
       
        // UI & core - the nodes to initially select and open will be overwritten by the cookie plugin

        // the UI plugin - it handles selecting/deselecting/hovering nodes
        "ui" : {
            // this makes the node with ID node_4 selected onload
            "initially_select" : [ "node_0" ]
        },
        // the core plugin - not many options here
    
    })
    
    .bind("create.jstree", function (e, data) {
        $.post(
            listtreeaction, 
            { 
                "operation" : "create_node", 
                "id" : data.rslt.parent.attr("id").replace("node_",""), 
                "position" : data.rslt.position,
                "title" : data.rslt.name,
                "type" : data.rslt.obj.attr("rel")
            }, 
            function (r) {
                if(r.status) {
                    $(data.rslt.obj).attr("id", "node_" + r.id);
                }
                else {
                    $.jstree.rollback(data.rlbk);
                }
            }
        );
    })
    /*
    .bind("remove.jstree", function (e, data) {
        data.rslt.obj.each(function () {
            $.ajax({
                async : false,
                type: 'POST',
                url: listtreeaction,
                data : { 
                    "operation" : "remove_node", 
                    "id" : this.id.replace("node_","")
                }, 
                success : function (r) {
                    if(!r.status) {
                        data.inst.refresh();
                    }
                }
            });
        });
    }) */
    .bind("rename.jstree", function (e, data) {
        $.post(
            listtreeaction, 
            { 
                "operation" : "rename_node", 
                "id" : data.rslt.obj.attr("id").replace("node_",""),
                "title" : data.rslt.new_name
            }, 
            function (r) {
                if(!r.status) {
                    $.jstree.rollback(data.rlbk);
                }
            }
        );
    })
/*    .bind("move_node.jstree", function (e, data) {
        data.rslt.o.each(function (i) {
            $.ajax({
                async : false,
                type: 'POST',
                url: listtreeaction,
                data : { 
                    "operation" : "move_node", 
                    "id" : $(this).attr("id").replace("node_",""), 
                    "ref" : data.rslt.cr === -1 ? 1 : data.rslt.np.attr("id").replace("node_",""), 
                    "position" : data.rslt.cp + i,
                    "title" : data.rslt.name,
                    "copy" : data.rslt.cy ? 1 : 0
                },
                success : function (r) {
                    if(!r.status) {
                        $.jstree.rollback(data.rlbk);
                    }
                    else {
                        $(data.rslt.oc).attr("id", "node_" + r.id);
                        if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
                            data.inst.refresh(data.inst._get_parent(data.rslt.oc));
                        }
                    }
                    $("#analyze").click();
                }
            });
        });
    });  */

});
