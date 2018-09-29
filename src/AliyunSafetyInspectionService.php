<?php
/**
 * Created by PhpStorm.
 * User: nfangxu
 * Date: 2018/9/29
 * Time: 13:24
 */

namespace Fangxu\SafetyInspection;


use Fangxu\SafetyInspection\SafetyInspection;
use Fangxu\SafetyInspection\SafetyInspectionException;
use Green\Request\V20180509 as Green;

class AliyunSafetyInspectionService implements SafetyInspection
{
    protected $config;
    protected $client;
    protected $region = [
        "cn-beijing" => "cn-beijing",
        "cn-shanghai" => "cn-shanghai",
    ];
    protected $textScenes = [
        "antispam", "keyword"
    ];
    protected $imageScenes = [
        "porn", "terrorism", "qrcode", "ad", "ocr"
    ];

    public function __construct()
    {
        $this->config = [
            "id" => env("ALIYUN_GREEN_ACCESS_KEY_ID"),
            "secret" => env("ALIYUN_GREEN_ACCESS_KEY_SECRET"),
            "region_id" => env("ALIYUN_REGION_ID"),
            "region_name" => $this->region[env("ALIYUN_REGION_ID")] ?? env("ALIYUN_REGION_ID"),
        ];
        $iClientProfile = \DefaultProfile::getProfile(
            $this->config["region_id"],
            $this->config['id'],
            $this->config['secret']
        );
        \DefaultProfile::addEndpoint(
            $this->config['region_name'],
            $this->config['region_id'],
            "Green",
            "green." . $this->config["region_id"] . ".aliyuncs.com");
        $this->client = new \DefaultAcsClient($iClientProfile);
    }

    public function text($text, $func)
    {
        $request = new Green\TextScanRequest();
        $request->setMethod("POST");
        $request->setAcceptFormat("JSON");
        $tasks = [];
        if (is_array($text)) {
            foreach ($text as $item) {
                $tasks[] = [
                    "dataId" => uniqid(),
                    "content" => $item
                ];
            }
        } else {
            $tasks[] = [
                'dataId' => uniqid(),
                'content' => $text
            ];
        }

        $success = [];

        foreach ($this->textScenes as $scene) {
            $request->setContent(json_encode([
                "tasks" => $tasks,
                "scenes" => [
                    $scene
                ],
            ]));
            $response = $this->client->getAcsResponse($request);
            if ($response->code == 200) {
                foreach ($response->data as $k => $taskResult) {
                    if (200 == $taskResult->code) {
                        $success[$k][] = $taskResult->results[0];
                        $func($taskResult->results[0]);
                    } else {
                        throw new SafetyInspectionException($response->msg, $response->code);
                    }
                }
            } else {
                throw new SafetyInspectionException($response->msg, $response->code);
            }
        }
        return true;
    }

    public function image($urls, $func)
    {
        $request = new Green\ImageSyncScanRequest();
        $request->setMethod("POST");
        $request->setAcceptFormat("JSON");
        $tasks = [];
        if (is_array($urls)) {
            foreach ($urls as $url) {
                $tasks[] = [
                    "url" => $url,
                    "time" => round(microtime(true) * 1000)
                ];
            }
        } else {
            $tasks[] = [
                "url" => $urls,
                "time" => round(microtime(true) * 1000)
            ];
        }

        $success = [];

        foreach ($this->imageScenes as $scene) {
            $request->setContent(json_encode([
                "tasks" => $tasks,
                "scenes" => [
                    $scene
                ],
            ]));
            $response = $this->client->getAcsResponse($request);
            if ($response->code == 200) {
                foreach ($response->data as $k => $taskResult) {
                    if (200 == $taskResult->code) {
                        $success[$k][] = $taskResult->results[0];
                    } else {
                        throw new SafetyInspectionException($response->msg, $response->code);
                    }
                }
            } else {
                throw new SafetyInspectionException($response->msg, $response->code);
            }
        }
        return $func($success);
    }
}