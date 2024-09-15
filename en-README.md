# JKOPay-Test-Environment | Self-Hosted Testing Environment for JKOPay

### Main purpose: Testing JKOPay API integration with the following functionalities:
- Create orders
- Query orders
- Refund orders

![image](https://imgur.com/4YlfQwT.png)

## Tutorial Steps
### 1. Modify the following parameters in `jkopay-backend.php`:

| Parameter | Description |
|--------|------|
| `STORE_ID` | Store ID provided by JKOPay |
| `API_KEY` | API Key provided by JKOPay |
| `SECRET_KEY` | SECRET_KEY provided by JKOPay |
| `RESULT_URL` | URL for payment result callback, use your domain (e.g., https://testjkopay.mrbandi.one/) |
| `RESULT_DISPLAY_URL` | URL to return to after payment completion |
> After configuration, deploy it to a VPS or rented system, then access the configured URL in your browser (e.g., https://testjkopay.mrbandi.one/)

### 2. Create an Order
Enter the amount you want to test and click create. Then scan the QR code using the test app provided by JKOPay.

<img src="https://imgur.com/zJ6609J.png" width="250">

#### Result: Payment Successful

<img src="https://imgur.com/6bDqo0D.png" width="250">

#### Result: Payment Failed
> If you encounter this situation, please notify me via Discord for immediate modifications.

<img src="https://imgur.com/FDVdSVv.png" width="250">

### 3. Order Query
Copy the order number you just created, which might look like `TEST-ORDER-1724538432`, then click query.
For order status code references, please check this website: **[View JKOPay API Documentation](https://open-doc.jkos.com/?docs=%e7%b7%9a%e4%b8%8a%e6%94%af%e4%bb%98onlinepay/api%e5%88%97%e8%a1%a8/%e4%bb%a3%e7%a2%bc%e6%84%8f%e7%be%a9)**

#### Query Result: Payment Pending

<img src="https://imgur.com/cYvoQVS.png" width="250">

#### Query Result: Payment Successful

<img src="https://imgur.com/pN3BMlm.png" width="250">

### 4. Order Refund
Test if the refund functionality is working correctly.

#### Refund Successful

<img src="https://imgur.com/gIh2ggg.png" width="250">


### 5. Check Transaction Records in JKOPay Test App
#### Payment Successful

<img src="https://imgur.com/YhYOsIG.png" width="250">

#### Refund Successful

<img src="https://imgur.com/LQKlvMb.png" width="250">




