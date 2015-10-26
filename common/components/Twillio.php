<?php
/**
 * @author Bryan Tan <bryantan16@gmail.com>
 */
/*namespace common\components;
namespace common\components\twillio;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;*/

namespace common\components;
 
use Yii;
use yii\base\Component;

class Twillio extends Component
{
    public $sid;
    public $token;

    private $_client = null;
    private $_clientCapability = null;

    public function init()
    {
        if (!$this->sid) {
            throw new InvalidConfigException('SID is required');
        }
        if (!$this->token) {
            throw new InvalidConfigException('Token is required');
        }
    }

    public function getClient()
    {
        if ($this->_client === null) {
            $client = new \Services_Twilio($this->sid, $this->token);

            $this->_client = $client;
        }

        return $this->_client;
    }

    public function getClientCapability()
    {
        if ($this->_clientCapability === null) {
            $client = new \Services_Twilio_Capability($this->sid, $this->token);

            $this->_clientCapability = $client;
        }

        return $this->_clientCapability;
    }
} 