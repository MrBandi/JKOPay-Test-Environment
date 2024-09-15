# JKOPay-Test-Environment | 街口支付 自架測試環境

測試環境 編寫日期：2024-09-15
街口支付 API 版本：2.1.0 (官方初版)

### 主要用於街口支付 API 串接測試，測試功能：
- 創建訂單
- 查詢訂單
- 訂單退款

![image](https://imgur.com/4YlfQwT.png)

## 教學步驟
### 1. 修改 `jkopay-backend.php` 以下相關参数：

| 參數 | 參數說明 |
|--------|------|
| `STORE_ID` | 街口支付 提供的 Store ID |
| `API_KEY` | 街口支付 提供的 API Key |
| `SECRET_KEY` | 街口支付提供的 SECRET_KEY |
| `RESULT_URL` | 付款結果回傳URL，使用你的網域（例如：https://testjkopay.mrbandi.one/） |
| `RESULT_DISPLAY_URL` | 付款完成後回到的主頁面URL |
> 設定完之後，放入VPS或租借的系統，接著前往瀏覽器輸入設定好的網址（例如：https://testjkopay.mrbandi.one/ ）

### 2. 創建訂單
輸入想要測試的金額，按下創建。接著用街口支付給的測試APP去掃描。

<img src="https://imgur.com/zJ6609J.png" width="250">

#### 結果：付款成功

<img src="https://imgur.com/6bDqo0D.png" width="250">

#### 結果：付款失敗
> 如發生這樣狀況，請透過 Discord 告知我，立即做修改。

<img src="https://imgur.com/FDVdSVv.png" width="250">

### 3. 訂單查詢
複製剛剛創建好的訂單編號，可能是 `TEST-ORDER-1724538432` 類似這樣，之後按下查詢。
訂單狀態碼對應請查看這個網站，**[點我查看 街口支付API文件](https://open-doc.jkos.com/?docs=%e7%b7%9a%e4%b8%8a%e6%94%af%e4%bb%98onlinepay/api%e5%88%97%e8%a1%a8/%e4%bb%a3%e7%a2%bc%e6%84%8f%e7%be%a9)**

#### 尚未付款查詢結果

<img src="https://imgur.com/cYvoQVS.png" width="250">

#### 付款成功查詢結果

<img src="https://imgur.com/pN3BMlm.png" width="250">

### 4. 訂單退款
測試退款功能是否正常。

#### 退款成功

<img src="https://imgur.com/gIh2ggg.png" width="250">


### 5. 查看街口測試APP 交易紀錄
#### 付款成功

<img src="https://imgur.com/YhYOsIG.png" width="250">

#### 退款成功

<img src="https://imgur.com/LQKlvMb.png" width="250">




    
