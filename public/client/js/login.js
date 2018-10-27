var AppId = null
var AppSecret = null

function statusChangeCallback(response) {
    if (response.status === 'connected') {
        connectedFb();
    } else {
        FB.login(function(response) {
            connectedFb();
        }, {scope: 'email, public_profile, manage_pages, pages_messaging_subscriptions,publish_pages,read_page_mailboxes,pages_messaging'});
    }
}

function connectedFb() {
    $('.background-loading').css({'display': 'block'});
    var auth = FB.getAuthResponse();
    var accessTokenShort = auth.accessToken;
    $.ajax({
        url: "https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id="+AppId+"&client_secret="+AppSecret+"&fb_exchange_token=" + accessTokenShort,
        type: "GET",
        dataType: "JSON",
        success: function(res) {
            var accessTokenLong = res.access_token;
            login(auth.userID, accessTokenLong);
        }
    })
}

function checkLoginState() {
    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });
}


(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v3.0&appId=' + AppId;
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function login(id, accessToken) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "/login",
        data: {
            id: id,
            access_token: accessToken,
            app: 'WEB'
        },
        type: "POST",
        success: function(rp) {
            if (rp.authenticated) {
                localStorage.setItem('api_token', rp.api_token);
                localStorage.setItem('uid', rp.uid);
                localStorage.setItem('sk_token', rp.jwt);
                window.location.replace(rp.router)
            } else {
                $.notify("Lỗi đăng nhập vui lòng làm mới lại trang rồi đăng nhập lại");
            }
        },
        error: function(err) {
            // window.location.href = '/';
            $.notify("Lỗi đăng nhập vui lòng làm mới lại trang rồi đăng nhập lại");
        }
    })
}

function getPageAdmins(fbPageId) {
    var panelRole = $('#roles-panel');
    var api_token = getCookie('tk_cs');
    if (!panelRole.hasClass('hidden')) {
        panelRole.addClass('hidden');
    }
    $('#loadding-staff').css({
        'display': 'block'
    });
    $('#list-page-admins').empty();
    $('#id_page_set').val(fbPageId);
    $("#form-set-perms").find("input[type='radio']").prop('checked', false);
    $.ajax({
        url: '/api/pages/' + fbPageId + '/staffs',
        type: "GET",
        dataType: "JSON",
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        success: function(resp) {
            var pageAdmins = resp.members;
            var idUser = resp.user_fb_id;
            for (var i = 0; i < pageAdmins.length; i++) {
                if (pageAdmins[i]['id'] != idUser) {
                    var html = '<li onclick="boldRow($(this)); getRolesStaff(' + pageAdmins[i].id + ',' + fbPageId + ')">' +
                        '<a class="pages-manager">' +
                        '<img src="https://graph.facebook.com/' + pageAdmins[i].id + '/picture?height=40&width=40">' +
                        '<span class="txtNameStaff">' + pageAdmins[i].name + '</span>';

                    if (pageAdmins[i].hasRole && pageAdmins[i].hasRole.display_name) {
                        html += '<div class="list-page-staff list-style-none">';
                        html += '<div class="li-page-staff">'+
                                    pageAdmins[i].hasRole.display_name +
                                '</div>';
                    }
                    var pages = pageAdmins[i].pages;
                    for (var j = 0; j < pages.length; j++) {
                        html += '<div class="li-roles">'+
                                    '<img title="'+pages[j].page_name+'" src="https://graph.facebook.com/' + pages[j].fb_page_id + '/picture?height=40&width=40">' +
                                    '<div class="destroyBtn" title="Xóa bỏ quyền với Fanpage" onclick="destroyRole(' + pageAdmins[i].id + ',' + pages[j].fb_page_id + ')">' +
                                        '<i class="fa fa-trash"></i>' +
                                    '</div>' +
                                '</div>';
                    }
                    if (pageAdmins[i].hasRole && pageAdmins[i].hasRole.display_name) {
                        html += '</div>';
                    }             
                    html += '</a>';
                    if (pageAdmins[i].hasRole && pageAdmins[i].hasRole.display_name) {
                        html += '<span class="destroyStaff" onclick="destroyStaff(' + pageAdmins[i].id + ')" title="Xóa bỏ nhân viên">' +
                                    '<i class="fa fa-close"></i>' +
                                '</span>';
                    }
                    if (pageAdmins[i].pendding) {
                        html += '<div class="text-danger">Đang chờ</div>';
                    }       
                    html += '</li>';
                    $('#list-page-admins').append(html);
                }
            }
            $('#loadding-staff').css({
                'display': 'none'
            });
            $("#mems_panel").removeClass('hidden');
        },
        error: function(err) {
            if (err.status == 403) {
                $.notify('Bạn không có quyền thực hiện tác vụ','error');
            }
        }
    })
}

function getRolesStaff(fbIdStaff, fbPageId) {
	var api_token = getCookie('tk_cs');
    $('#loadding-roles').css({
        'display': 'block'
    });
    $("#form-set-perms").find("input[type='radio']").prop('checked', false);
    $('#fb_staff_set').val(fbIdStaff);
    $.ajax({
        url: '/api/pages/'+ fbPageId +'/staffs/' + fbIdStaff,
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        type: "GET",
        dataType: "JSON",
        success: function(res) {
            switch (res.name) {
                case "SALER":
                    $('#saleRole').prop('checked', true);
                    break;
                case "STORAGER":
                    $('#warehouseRole').prop('checked', true);
                    break;
                case "MANAGER":
                    $('#managerRole').prop('checked', true);
                    break;
            }
            $('#loadding-roles').css({
                'display': 'none'
            });
            $('#roles-panel').removeClass('hidden');
        }, 
        error: function(err) {
            if (err.status == 403) {
                $.notify('Bạn không có quyền thực hiện tác vụ này', 'error');
            }
        }
    })
}

function setRolesStaff() {
	var api_token = getCookie('tk_cs');
    $("#loadding_setup_role").removeClass('hidden');
    $("#btn_setup_role").hide();
    var data = $('#form-set-perms').serialize();
    var fbIdStaff = $("#fb_staff_set").val();
    var fbPageId = $("#id_page_set").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: "../api/pages/roles/update/" + fbPageId + "/" + fbIdStaff,
        data: data,
        type: "POST",
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        dataType: "JSON",
        success: function(res) {
            if (res.success) {
                getPageAdmins($("#id_page_set").val());
                $.notify('Lưu thành công! Click vào tiếp tục để vào trang quản trị', "success");
            }
            $("#loadding_setup_role").addClass("hidden");
            $("#btn_setup_role").show();
        },
        error: function(err) {
            if (err.status == 403) {
                $.notify('Bạn không có quyền sử dụng chức năng này', 'error');
            };
            if (err.status == 422) {
                var errors = err.responseJSON.errors;
                for (var k in errors) {
                    $.notify(errors[k],'error');
                }
            }
            if (err.status == 500) {
                $.notify(err.responseJSON.message,'error');
            }
            $("#loadding_setup_role").addClass("hidden");
            $("#btn_setup_role").show();
        }
    })
}

function boldRow(obj) {
    obj.parent().children('.active').removeClass('active');
    obj.addClass('active');
}

function confirmWithRole(userParent) {
	var api_token = getCookie('tk_cs');
    $("#loadding_conf_role").removeClass("hidden");
    $(".btn_conf_role").hide();
    $.ajax({
        url: "/api/roles/invite/confirm/" + userParent,
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        type: "POST",
        dataType: "JSON",
        success: function(res) {
            if (res.success) {
                $.notify('Thành công', 'success');
                if (res.role.name != 'STORAGER') {
                    window.location.replace('/conversations');
                } else {
                    window.location.replace('/product')
                }
            }
            $(".btn_conf_role").show();
            $("#loadding_conf_role").addClass("hidden");
        },
        error: function(err) {
            $.notify('Có lỗi xảy ra', 'error');
            $(".btn_conf_role").show();
            $("#loadding_conf_role").addClass("hidden");
        }
    })
}

function denyWithRole(userParent) {
	var api_token = getCookie('tk_cs');
    $("#loadding_conf_role").removeClass("hidden");
    $(".btn_conf_role").hide();
    $.ajax({
        url: "/api/roles/invite/deny/" + userParent,
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        type: "DELETE",
        dataType: "JSON",
        success: function(res) {
            if (res.success) {
                $.notify('Thành công', 'success');
                $("#box_" + userParent).remove();
                var count = $(".boxItemInvite");
                if (!count.length) {
                    denyAllInvite()
                }
            }
            $(".btn_conf_role").show();
            $("#loadding_conf_role").addClass("hidden");
        },
        error: function(err) {
            $.notify('Có lỗi xảy ra', 'error');
            $(".btn_conf_role").show();
            $("#loadding_conf_role").addClass("hidden");
        }
    })
}

/**
 * clear all invite
 * @return {[type]} [description]
 */
function denyAllInvite() {
	var api_token = getCookie('tk_cs');
    $("#loadding_conf_role").removeClass("hidden");
    $(".btn_conf_role").hide();
    $.ajax({
        url: "/api/roles/invite/clear",
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        type: "DELETE",
        dataType: "JSON",
        success: function(res) {
            if (res.success) {
                $.notify('Bạn đã từ chối tất cả đề nghị', 'success');
            }
            window.location.replace('/conversations');
            $(".btn_conf_role").show();
            $("#loadding_conf_role").addClass("hidden");
        },
        error: function(err) {
            $(".btn_conf_role").show();
            $.notify('Có lỗi xảy ra', 'error');
            $("#loadding_conf_role").addClass("hidden");
        }
    })
}

function destroyRole(fbIdStaff, fbPageId) {
	var api_token = getCookie('tk_cs');
    var panelRole = $('#roles-panel');
    var panelMem = $('#mems_panel');
    if (!panelRole.hasClass('hidden')) {
        panelRole.addClass('hidden');
    }
    if (!panelMem.hasClass('hidden')) {
        panelMem.addClass('hidden');
    }
    $('#loadding-staff').css({
        'display': 'block'
    });
    $("#form-set-perms").find("input[type='radio']").prop('checked', false);
    $('#list-page-admins').empty();
    $.ajax({
        url: "/api/pages/roles/destroy/" + fbPageId + "/" + fbIdStaff,
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        type: "POST",
        dataType: "JSON",
        success: function(res) {
            if (res.success) {
                getPageAdmins($("#id_page_set").val());
                $.notify('Xóa bỏ quyền thành công', 'success');
                $('#loadding-staff').css({
                    'display': 'none'
                });
                panelRole.removeClass('hidden');
                panelMem.removeClass('hidden');
            }
        },
        error: function(err) {
            $.notify('Không thể xóa bỏ quyền', 'error');
            $('#loadding-staff').css({
                'display': 'none'
            });
            panelRole.removeClass('hidden');
            panelMem.removeClass('hidden');
        }
    })
}

function destroyStaff (fbIdStaff)
{
	var api_token = getCookie('tk_cs');
    var panelRole = $('#roles-panel');
    var panelMem = $('#mems_panel');
    if (!panelRole.hasClass('hidden')) {
        panelRole.addClass('hidden');
    }
    if (!panelMem.hasClass('hidden')) {
        panelMem.addClass('hidden');
    }
    $('#loadding-staff').css({
        'display': 'block'
    });
    $("#form-set-perms").find("input[type='radio']").prop('checked', false);
    $('#list-page-admins').empty();
    $.ajax({
        url: "/api/pages/staffs/destroy/" + fbIdStaff,
        headers: {
            'Authorization': 'Bearer ' + api_token
        },
        type: "POST",
        dataType: "JSON",
        success: function(res) {
            if (res.success) {
                getPageAdmins($("#id_page_set").val());
                $.notify('Xóa bỏ nhân viên thành công', 'success');
                $('#loadding-staff').css({
                    'display': 'none'
                });
                panelRole.removeClass('hidden');
                panelMem.removeClass('hidden');
            }
        },
        error: function(err) {
            $.notify('Không thể xóa bỏ nhân viên', 'error');
            $('#loadding-staff').css({
                'display': 'none'
            });
            panelRole.removeClass('hidden');
            panelMem.removeClass('hidden');
        }
    })
}

function getCookie (cname) {
  var name = cname + '='
  var decodedCookie = decodeURIComponent(document.cookie)
  var ca = decodedCookie.split(';')
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i]
    while (c.charAt(0) === ' ') {
      c = c.substring(1)
    }
    if (c.indexOf(name) === 0) {
      return c.substring(name.length, c.length)
    }
  }
  return ''
}