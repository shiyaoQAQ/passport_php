function request(formData) {
    let {
        url,
        method,
        success,
        data = null,
        fail = () => {},
        complete = () => {},
    } = formData;
    $.ajax({
        url: url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
        },
        type: method,
        data: data,
        success: (res)=> {
            if (success instanceof Function) {
                if (res.code == 0) {
                    success(res);
                }else {
                    alert(res.msg);
                }
            }
        },
        fail: (res) => {
            alert('请求失败！')
            fail(res)
        },
        complete: (res) => {
            complete(res.responseJSON)
        }
    })
}

module.exports = {
    request,
}