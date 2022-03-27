<?php
return [
    'id'              => 'ID',
    'name'            => 'Tên khách hàng',
    'first_name'      => 'Tên',
    'first_name_kana' => 'Tên KANA',
    'last_name'       => 'Họ',
    'last_name_kana'  => 'Họ KANA',
    'email'           => 'Địa chỉ email',
    'address'         => 'Địa chỉ',
    'address1'        => 'Tỉnh/Thành',
    'address2'        => 'Quận/Huyện',
    'address3'        => 'Địa chỉ 3',
    'country'         => 'Quốc gia',
    'company'         => 'Công ty',
    'postcode'        => 'Mã bưu chính',
    'sex'             => 'Giới tính',
    'birthday'        => 'Sinh nhật',
    'created_at'      => 'Tạo lúc',
    'updated_at'      => 'Cập nhật',
    'phone'           => 'Số điện thoại',
    'date'            => 'Ngày tạo',
    'status'          => 'Trạng thái',
    'group'           => 'Nhóm',
    'password'        => 'Mật khẩu',
    'total_order'     => 'Tổng đơn hàng',
    'total_amount'    => 'Tổng số tiền',
    'admin'                   => [
        'title'               => 'Quản lý khách hàng',
        'create_success'      => 'Tạo item thành công!',
        'edit_success'        => 'chỉnh sửa item thành công!',
        'list'                => 'Danh sách khách hàng',
        'action'              => 'Hành động',
        'delete'              => 'Xóa',
        'edit'                => 'Sửa',
        'add_new'             => 'Thêm mới',
        'add_new_title'       => 'Tạo khách hàng',
        'add_new_des'         => 'Tạo mới một khách hàng',
        'export'              => 'Xuất',
        'refresh'             => 'Làm mới',
        'result_item'         => 'Hiển thị <b> :item_from</b> tới <b> :item_to</b> trong số <b> :item_total</b> kết quả</b>',
        'sort'                => 'Sắp xếp',
        'select_country'      => 'Chọn quốc gia',
        'keep_password'       => '<i class    = "fa fa-info-circle"></i> Để trống nếu không muốn thay đổi mật khẩu',

        'sort_order'          => [
            'id_asc'          => 'ID asc',
            'id_desc'         => 'ID desc',
            'first_name_asc'  => 'Tên a-z',
            'first_name_desc' => 'Tên z-a',
            'last_name_asc'   => 'Họ a-z',
            'last_name_desc'  => 'Họ z-a',
        ],
        'search'              => 'Tìm kiếm',
        'search_place'        => 'Tìm tên, ID hoặc email',
        'search_order'        => [
            'status'          => 'Tất cả trạng thái',
        ],
    ],
    'config_manager'          => [
        'title'          => ' Cấu hình khách hàng',
        'lastname'       => 'Sử dụng HỌ',
        'name_kana'      => 'Sử dụng tên KANA',
        'firstname_kana' => 'Sử dụng tên KANA',
        'lasttname_kana' => 'Sử dụng họ KANA',
        'address1'       => 'Sử dụng Tỉnh/Thành',
        'address2'       => 'Sử dụng Quận/Huyện',
        'address3'       => 'Sử dụng Phường/Xã',
        'company'        => 'Sử dụng Công Ty',
        'postcode'       => 'Sử dụng Mã bưu chính',
        'country'        => 'Sử dụng Quốc Gia',
        'group'          => 'Sử dụng Nhóm',
        'birthday'       => 'Sử dụng NGÀY SINH',
        'sex'            => 'Sử dụng GIỚI TÍNH',
        'phone'          => 'Sử dụng SỐ ĐIỆN THOẠI',
        'required'       => 'Bắt buộc',
    ]
];
