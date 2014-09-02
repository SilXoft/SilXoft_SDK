<?php
namespace Sl\Listener\View\Controller;

interface Controller {
    
    public function onBeforeListViewTableButtons(\Sl_Event_View $event);
	public function onBeforeListViewTable(\Sl_Event_View $event);
    public function onAfterListViewTable(\Sl_Event_View $event);
}