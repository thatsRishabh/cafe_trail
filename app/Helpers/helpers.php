<?php

function prepareResult($error, $data, $msg)
{
	return ['success' => $error, 'data' => $data, 'message' => $msg];
}
