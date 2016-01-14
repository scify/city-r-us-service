<?php


class TestRadicalConfigurationApi extends TestCase {

    public function TestRegisterService()
    {
        $radicalIntegrationManager  = new \App\Services\Radical\RadicalIntegrationManager();
        $response = $radicalIntegrationManager->registerMission(array("id"=>"1","name"=>"test","description"=>"test service"));
        $this->assertTrue($response);
    }

}
