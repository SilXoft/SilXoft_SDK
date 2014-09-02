<?php
namespace Sl\Listener\View\Controller;

interface Listtable {
    
    public function onBeforeListViewTableGroupButton(\Sl_Event_View $event);
    public function onListViewTableLegendButtons(\Sl_Event_View $event);
    
	
}