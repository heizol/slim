# wxlink\DefaultApi

All URIs are relative to *http://way.jd.com/51daas*

Method | HTTP request | Description
------------- | ------------- | -------------
[**qryCreditMultiData**](DefaultApi.md#qryCreditMultiData) | **GET** /qryCreditMultiData | 查询多项信息


# **qryCreditMultiData**
> string qryCreditMultiData($name, $card_num, $appkey)

查询多项信息

查询多项信息

### Example
```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$api_instance = new wxlink\Api\DefaultApi();
$name = "张晓玮"; // string | 姓名
$card_num = "321321198508193414"; // string | 身份证号
$appkey = "appkey_example"; // string | 万象平台提供的appkey

try {
    $result = $api_instance->qryCreditMultiData($name, $card_num, $appkey);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->qryCreditMultiData: ', $e->getMessage(), PHP_EOL;
}
?>
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| 姓名 | [default to 张晓玮]
 **card_num** | **string**| 身份证号 | [default to 321321198508193414]
 **appkey** | **string**| 万象平台提供的appkey |

### Return type

**string**

### Authorization

No authorization required

### HTTP request headers

 - **Content-Type**: text/plain
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to Model list]](../../README.md#documentation-for-models) [[Back to README]](../../README.md)

