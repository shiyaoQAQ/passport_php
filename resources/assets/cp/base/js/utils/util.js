function request(formData) {
    let {
        url,
        method,
        success,
        data = null,
        error = null,
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
                } else {
                    // 判断是否有错误回调
                    if (error == null) {
                        this.$Modal.error({
                            title: '操作失败',
                            content: res.msg + ' ' + res.code,
                        });
                    } else {
                        error(res)
                    }

                }
            }
        },
        fail: (res) => {
            // 判断是否有错误回调
            if (error == null) {
                this.$Modal.error({
                    title: '请求失败',
                    content: '请求失败！',
                });
            } else {
                error(res)
            }
        },
        complete: (res) => {
            complete(res.responseJSON)
        }
    })
}

module.exports = {
    request,
}
