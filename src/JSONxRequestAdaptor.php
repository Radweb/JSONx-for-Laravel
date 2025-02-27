<?php

namespace danharper\LaravelJSONx;

use danharper\JSONx\JSONx;
use Illuminate\Http\Request;

class JSONxRequestAdaptor {

	/**
	 * @var JSONx
	 */
	private $converter;

	public function __construct(JSONx $converter)
	{
		$this->converter = $converter;
	}

	public function handle(Request $request)
	{
		if ($this->givingXml($request))
		{
			$request->headers->set('Content-Type', 'application/json');
			$request->replace($this->fromJSONx($request));
		}

		return $request;
	}

	private function givingXml(Request $request)
	{
		return $request->header('Content-Type') === 'application/xml';
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	private function fromJSONx(Request $request)
	{
		$json = $this->converter->fromJSONx($request->getContent());

		// XML -> JSON converter makes PHP objects, but for the Request
		// we want PHP assoc arrays. Just casting to (array) isn't enough
		// as we want to convert all nested objects too for dot-notation access
		return json_decode(json_encode($json), true);
	}

}