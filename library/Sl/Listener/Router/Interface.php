<?php

interface Sl_Listener_Router_Interface {
    
    public function onRouteStartup(Sl_Event_Router $event);
    public function onRouteShutdown(Sl_Event_Router $event);
    public function onPreDispatch(Sl_Event_Router $event);
    public function onPostDispatch(Sl_Event_Router $event);
    public function onDispatchLoopStartup(Sl_Event_Router $event);
    public function onDispatchLoopShutdown(Sl_Event_Router $event);
    public function onGetRequest(Sl_Event_Router $event);
    public function onGetResponse(Sl_Event_Router $event);
    public function onSetRequest(Sl_Event_Router $event);
    public function onSetResponse(Sl_Event_Router $event);
    public function onBeforeInit(Sl_Event_Router $event);
    public function onAfterInit(Sl_Event_Router $event);
}

?>
