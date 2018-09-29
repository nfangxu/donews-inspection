> example: 
```
Route::get("/inspection", function (\App\Contracts\SafetyInspection $inspection) {
    $inspection->text("新年快乐", function ($result) {
        if ($result->suggestion != "pass") {
            throw new \Exception("内容中含有不合法的字段", "405");
        }
    });
});
```
