<?php

interface SelectCriterion extends Criterion
{

	/**
	 *
	 * @return string
	 */
	public function createSelectSql();

	/**
	 *
	 * @return string
	 */
	public function createFromSql();

	/**
	 *
	 * @return string
	 */
	public function createJoinSql();

	/**
	 *
	 * @return string
	 */
	public function createWhereSql();

	/**
	 *
	 * @return string
	 */
	public function createGroupSql();

	/**
	 *
	 * @return string
	 */
	public function createHavingSql();

	/**
	 *
	 * @return string
	 */
	public function createOrderSql();

	/**
	 *
	 * @return string
	 */
	public function createLimitSql();

}

