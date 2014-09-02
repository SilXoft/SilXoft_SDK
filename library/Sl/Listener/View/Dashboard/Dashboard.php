<?php
namespace Sl\Listener\View\Dashboard;

interface Dashboard {
    
    public function onDashboard(\Sl_Event_View $event);
}