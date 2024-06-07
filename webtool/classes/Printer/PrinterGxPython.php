# <?php die('Access Deny');?>
# -*- coding: utf-8 -*-
'''
yum install python3
如果提示requests模块不存在
pip install --upgrade pip
pip install requests
如果提示secrets模块不存在
pip install python-secrets -i https://pypi.tuna.tsinghua.edu.cn/simple
如果上面的secrets不能安装可尝试
pip install virtualenv
virtualenv myenv
source myenv/bin/activate 
后再执行 
如果报urllib3错误，说明 urllib3与chardet版本不一致，使用以下方式解决
pip uninstall urllib3 chardet 
pip install --upgrade requests
 
# -*- coding: utf-8 -*-

获取printer_id
python PrinterGxPython.php  -url cloudprint/printer/connectid -d '{"serial_number": ""}' 
'''

import sys
import datetime
import hashlib
import hmac
import json
import requests
import base64
import secrets 
import json 
access_key = ""
secret_key = ""
apikey = ""

access_key = ""
secret_key = ""
apikey = ""

params = {}

# 当命令行有传参的时候我们才执行，因为我这里需要外部赋值，所以要求长度大于3，如果不需要赋值直接传参只需要大于2即可
if len(sys.argv) >= 3:
    for i in range(0, len(sys.argv) - 1):
        j = i * 2 + 1
        if j + 1 >= len(sys.argv):
            break
        params[sys.argv[j]] = sys.argv[j + 1] 
# 发起请求
request_body =  json.loads(params['-d']) 

dns = "cloudprint.cib-biz.com.cn"
host = dns
region = "cn-north-1"  

connectid_path = "/"+params['-url']
 
#exit()


#######################################
## 以下代码不用修改
#######################################

def call_req(request_body):
    header = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Host': host,
        'X-Api-Key': apikey,
        'X-Api-Version': '1.0'
    }

    url = 'https://' + dns + connectid_path
    #print('url: ', url)
    #exit()
    # get auth header for IAM
    auth_header = get_authorization_header(
        access_key, secret_key, connectid_path,
        HttpConsts.post_method, region,
        request_body, header
    )

    response = request(
        HttpConsts.post_method,
        url,
        auth_header,
        request_body
        )

    return response['status_code'], response['response_body']


class HttpConsts:

    # http status code
    success = 200
    created = 201
    no_contents_success = 204
    bad_request = 400
    unauthorized = 401
    not_found = 404
    method_not_allowed = 405
    conflict = 409
    unsupported_media_type = 415
    internal_server_error = 500
    service_unavailable = 503

    get_method = 'GET'
    post_method = 'POST'
    put_method = 'PUT'
    delete_method = 'DELETE'

def sign(key, msg):
    '''
    calculate hmacsha256 value
    '''
    return hmac.new(key, msg.encode('utf-8'), hashlib.sha256).digest()

def get_signature_key(key, date_stamp, region_name, service_name):
    '''
    calculate signature value for iam
    '''
    k_date = sign(('AWS4' + key).encode('utf-8'), date_stamp)
    k_region = sign(k_date, region_name)
    k_service = sign(k_region, service_name)
    k_signing = sign(k_service, 'aws4_request')
    return k_signing

def get_canonical_header(header):
    '''
    get canonical header from header json object
    '''
    canonical_header = ''
    lower_header = {}

    # convert key lower case
    for key in header.keys():
        lower_header[key.lower()] = header[key]

    sorted_header = sorted(lower_header.items())

    # create canonical and signed header
    for val in sorted_header:
        canonical_header += val[0].lower() + ':' + val[1] + '\n'

    return canonical_header

def get_signed_header(header):
    '''
    get signed header from header json object
    '''
    signed_header = ''
    lower_header = {}

    # convert key lower case
    for key in header.keys():
        lower_header[key.lower()] = header[key]

    sorted_header = sorted(lower_header.items())

    # create canonical and signed header
    for val in sorted_header:
        signed_header += val[0].lower() + ';'

    # remove last ;
    return signed_header[:-1]

def get_authorization_header(
        access_key, secret_key,
        canonical_uri, method,
        region, request_parameters, header, service='execute-api'
    ):
    '''
    get authorization header for iam
    '''

    utc_now = datetime.datetime.utcnow()
    amz_date = utc_now.strftime('%Y%m%dT%H%M%SZ')
    date_stamp = utc_now.strftime('%Y%m%d')

    # TASK 0: add x-amz-date header
    header['X-Amz-Date'] = amz_date

    # TASK 1: CREATE A CANONICAL REQUEST
    canonical_querystring = ''

    canonical_headers = get_canonical_header(header)
    signed_headers = get_signed_header(header)

    payload_hash = hashlib.sha256(
        json.dumps(request_parameters).encode('utf-8')
    ).hexdigest()

    canonical_request = method + '\n' + canonical_uri + '\n' + \
        canonical_querystring + '\n' + canonical_headers + '\n' + \
        signed_headers + '\n' + payload_hash
    # print ("--- canonical request ----")
    # print (canonical_request)

    # TASK 2: CREATE THE STRING TO SIGN
    algorithm = 'AWS4-HMAC-SHA256'
    credential_scope = date_stamp + '/' + region + \
        '/' + service + '/' + 'aws4_request'

    string_to_sign = \
        algorithm + '\n' + amz_date + '\n' + credential_scope + '\n' + \
        hashlib.sha256(
            canonical_request.encode('utf-8')
        ).hexdigest()
    # print ("--- string to sign ----")
    # print (string_to_sign)

    # TASK 3: CALCULATE THE SIGNATURE
    signing_key = get_signature_key(
        secret_key,
        date_stamp,
        region,
        service
    )
    signature = hmac.new(signing_key, string_to_sign.encode(
        'utf-8'), hashlib.sha256).hexdigest()
    # TASK 4: ADD SIGNING INFORMATION TO THE REQUEST
    authorization_header = algorithm + ' ' + 'Credential=' + \
        access_key + '/' + credential_scope + ', ' + 'SignedHeaders=' + \
        signed_headers + ', ' + 'Signature=' + signature

    header['Authorization'] = authorization_header

    return header

def get_header_with_hash(hash_strings, header):
    '''
    get header with hash code
    '''

    random_code = base64.urlsafe_b64encode(secrets.token_bytes(8))[:8]
    header['X-Random-Code'] = random_code.decode('utf-8')
    header['X-Hash-Code'] = hashlib.sha256(
        hash_strings.encode('utf-8') + random_code).hexdigest()

    return header

def request(
        method, endpoint,
        header, request_parameters
    ):
    '''
    print("======= request =========")
    print(method, endpoint)
    print(json.dumps(header))
    print()
    print(json.dumps(request_parameters)) 
    '''
    
    if method == HttpConsts.get_method:
        response = requests.get(
            endpoint,
            headers=header,
            data=json.dumps(request_parameters),timeout=(60, 60)
            )
    elif method == HttpConsts.post_method:
        response = requests.post(
            endpoint,
            headers=header,
            data=json.dumps(request_parameters),timeout=(60, 60)
            )
    elif method == HttpConsts.put_method:
        response = requests.put(
            endpoint,
            headers=header,
            data=json.dumps(request_parameters),timeout=(60, 60)
            )
    elif method == HttpConsts.delete_method:
        response = requests.delete(
            endpoint,
            headers=header,
            data=json.dumps(request_parameters),timeout=(60,60)
            )
    else:
        return HttpConsts.internal_server_error, ''

     

    return {
        'status_code': response.status_code,
        'response_body': response.text
    }


status_code, res =  call_req(request_body) 
print(res)