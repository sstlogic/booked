<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface ISqlCommand
{
	/**
	 * @param Parameters $parameters
	 */
	public function SetParameters(Parameters $parameters);

	/**
	 * @param Parameter $parameter
	 */
	public function AddParameter(Parameter $parameter);

	/**
	 * @return string the underlying query to be executed
	 */
	public function GetQuery();

	/**
	 * @return bool
	 */
	public function ContainsGroupConcat();

	/**
     * @return bool
     */
	public function IsMultiQuery();

    /**
     * @return Parameters
     */
    public function GetParameters();
}