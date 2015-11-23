<?php


class TestRadicalConfigurationApi extends TestCase {

    public function TestRegisterService()
    {
        $radicalServiceConfiguration  = new \App\Services\Radical\RadicalConfigurationAPI();
        $response = $radicalServiceConfiguration->registerMission(array("id"=>"1","name"=>"test","description"=>"test service"));
        $this->assertTrue($response);
    }

}
