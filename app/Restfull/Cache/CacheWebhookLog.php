<?php
namespace Appkita\CI4Restfull\Cache;
use \CodeIgniter\Entity;
class CacheWebhookLog extends Entity
{
    protected $attributes = [];
    protected $start_time = ['key'=>null, 'value'=>null];
    protected $end_time = ['key'=>null, 'value'=>null];
    protected $duration = ['key'=>null, 'value'=>null];
    protected $timestamp = ['key'=>null, 'value'=>null];

    function __construct() {
        $this->createAttributes();
        parent::__construct();
    }
    
    /**
     * function create attribute from \config\webhook
     */
    public function createAttributes() {
        $config = new \Config\Webhook();
        if (\is_array($config->log_format_database)) {
            foreach($config->log_format_database as $ck=>$cv) {
                $this->attributes[$cv] = null;
                /**
                 * get array key attribute from spesific
                 */
                switch($ck) {
                    case 'start':
                        $this->start_time = ['key'=>$cv, 'value'=>\microtime(true)];
                    break;
                    case 'end':
                        $this->end_time = ['key'=>$cv, 'value'=>null];
                    break;
                    case 'duration':
                        $this->duration = ['key'=>$cv, 'value'=>null];
                    break;
                    case 'timestamp':
                        $this->timestamp = ['key'=>$cv, 'value'=>\date('Y-m-d H:i:s')];
                    break;
                }
            }
        }
    }

	/**
	 * Takes an array of key/value pairs and sets them as
	 * class properties, using any `setCamelCasedProperty()` methods
	 * that may or may not exist.
	 *
	 * @param array $data
	 *
	 * @return $this
	 */
	public function fill(array $data = null)
	{
		if (! is_array($data))
		{
			return $this;
		}

		foreach ($data as $key => $value)
		{
            if (!\in_array($key, [$this->start_time['key'], $this->end_time['key'], $this->duration['key'], $this->timestamp['key']])) {
			    $this->__set($key, $value);
            }
		}

		return $this;
	}

    public function toArray(bool $onlyChanged = false, bool $cast = true, bool $recursive = false): array
	{
		$this->_cast = $cast;
		$return      = [];

		$keys = array_keys($this->attributes);
		$keys = array_filter($keys, function ($key) {
			return strpos($key, '_') !== 0;
		});

		if (is_array($this->datamap))
		{
			$keys = array_diff($keys, $this->datamap);
			$keys = array_unique(array_merge($keys, array_keys($this->datamap)));
		}

		// we need to loop over our properties so that we
		// allow our magic methods a chance to do their thing.
		foreach ($keys as $key)
		{
			if ($onlyChanged && ! $this->hasChanged($key))
			{
				continue;
			}

			$return[$key] = $this->__get($key);

			if ($recursive)
			{
				if ($return[$key] instanceof Entity)
				{
					$return[$key] = $return[$key]->toArray($onlyChanged, $cast, $recursive);
				}
				elseif (is_callable([$return[$key], 'toArray']))
				{
					$return[$key] = $return[$key]->toArray();
				}
			}
		}

        $return[$this->start_time['key']] = $this->start_time['value'];
        $return[$this->timestamp['key']] = $this->timestamp['value'];
        $this->end_time['value'] = \microtime(true);
        $this->duration['value'] = $this->end_time['value'] - $this->start_time['value'];
        $return[$this->end_time['key']] = $this->end_time['value'];
        $return[$this->duration['key']] = $this->duration['value'];
		$this->_cast = true;
		return $return;
	}

}