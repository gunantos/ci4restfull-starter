<?php
namespace Appkita\CI4Restfull;

use \CodeIgniter\API\ResponseTrait;

class RestfullApi extends BaseController
{
    use ResponseTrait;

    protected $auth = ['jwt', 'basic', 'digest', 'key'];

	/**
	 * Return an array of resource objects, themselves in array format
	 *
	 * @return mixed
	 */
	public function index()
	{
		return $this->fail(lang('RESTful.notImplemented', ['index']), 501);
	}

	/**
	 * Return the properties of a resource object
	 *
	 * @param mixed $id
	 *
	 * @return mixed
	 */
	public function show($id = null)
	{
		return $this->fail(lang('RESTful.notImplemented', ['show']), 501);
	}

	/**
	 * Return a new resource object, with default properties
	 *
	 * @return mixed
	 */
	public function new()
	{
		return $this->fail(lang('RESTful.notImplemented', ['new']), 501);
	}

	/**
	 * Create a new resource object, from "posted" parameters
	 *
	 * @return mixed
	 */
	public function create()
	{
		return $this->fail(lang('RESTful.notImplemented', ['create']), 501);
	}

	/**
	 * Return the editable properties of a resource object
	 *
	 * @param mixed $id
	 *
	 * @return mixed
	 */
	public function edit($id = null)
	{
		return $this->fail(lang('RESTful.notImplemented', ['edit']), 501);
	}

	/**
	 * Add or update a model resource, from "posted" properties
	 *
	 * @param mixed $id
	 *
	 * @return mixed
	 */
	public function update($id = null)
	{
		return $this->fail(lang('RESTful.notImplemented', ['update']), 501);
	}

	/**
	 * Delete the designated resource object from the model
	 *
	 * @param mixed $id
	 *
	 * @return mixed
	 */
	public function delete($id = null)
	{
		return $this->fail(lang('RESTful.notImplemented', ['delete']), 501);
	}

}