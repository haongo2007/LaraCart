<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopEmailTemplate;
use App\Models\Admin\EmailTemplate;
use App\Http\Resources\EmailTemplateCollection;
use Validator;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        $dataSearch = request()->all();
        $data = EmailTemplate::getEmailTemplateListAdmin($dataSearch);
        return EmailTemplateCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $data = [
            'title' => trans('email_template.admin.add_new_title'),
            'subTitle' => '',
            'title_description' => trans('email_template.admin.add_new_des'),
            'icon' => 'fa fa-plus',
            'arrayGroup' => $this->arrayGroup(),
            'ET' => [],
            'url_action' => bc_route_admin('admin_email_template.create'),
        ];
        return view($this->templatePathAdmin.'EmailTemplate.add_edit')
            ->with($data);
    }

/**
 * Post create new item in admin
 * @return [type] [description]
 */
    public function postCreate()
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required',
            'group' => 'required',
            'text' => 'required',
        ]);

        if ($validator->fails()) {
            // dd($validator->messages());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $dataInsert = [
            'name'     => $data['name'],
            'group'    => $data['group'],
            'text'     => $data['text'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'store_id' => session('adminStoreId'),
        ];
        ShopEmailTemplate::createEmailTemplateAdmin($dataInsert);

        return redirect()->route('admin_email_template.index')->with('success', trans('email_template.admin.create_success'));

    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $emailTemplate = AdminEmailTemplate::getEmailTemplateAdmin($id);
        if (!$emailTemplate) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = [
            'title' => trans('email_template.admin.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'ET' => $emailTemplate,
            'arrayGroup' => $this->arrayGroup(),
            'url_action' => bc_route_admin('admin_email_template.edit', ['id' => $emailTemplate['id']]),
        ];
        return view($this->templatePathAdmin.'EmailTemplate.add_edit')
            ->with($data);
    }

/**
 * update status
 */
    public function postEdit($id)
    {
        $emailTemplate = AdminEmailTemplate::getEmailTemplateAdmin($id);
        if (!$emailTemplate) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required',
            'group' => 'required',
            'text' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $dataUpdate = [
            'name'     => $data['name'],
            'group'    => $data['group'],
            'text'     => $data['text'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'store_id' => session('adminStoreId'),
        ];
        $emailTemplate->update($dataUpdate);

        return redirect()->route('admin_email_template.index')->with('success', trans('email_template.admin.edit_success'));

    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => trans('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if(!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(['error' => 1, 'msg' => trans('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
            }
            ShopEmailTemplate::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Get list variables support for email template
     *
     * @return json
     */
    public function listVariable()
    {
        $key = request('key');
        $list = [];
        switch ($key) {
            case 'order_success_to_customer':
            case 'order_success_to_admin':
                $list = [
                    '$title',
                    '$orderID',
                    '$toname',
                    '$firstName',
                    '$lastName',
                    '$address',
                    '$address1',
                    '$address2',
                    '$address3',
                    '$email',
                    '$phone',
                    '$comment',
                    '$orderDetail',
                    '$subtotal',
                    '$shipping',
                    '$discount',
                    '$total',
                ];
                break;

            case 'forgot_password':
                $list = [
                    '$title',
                    '$reason_sednmail',
                    '$note_sendmail',
                    '$note_access_link',
                    '$reset_link',
                    '$reset_button',
                ];
                break;

            case 'customer_verify':
                $list = [
                    '$title',
                    '$reason_sednmail',
                    '$note_sendmail',
                    '$note_access_link',
                    '$url_verify',
                    '$button',
                ];
                break;

            case 'contact_to_admin':
                $list = [
                    '$title',
                    '$name',
                    '$email',
                    '$phone',
                    '$content',
                ];
                break;
            case 'welcome_customer':
                    $list = [
                        '$title',
                        '$first_name',
                        '$last_name',
                        '$email',
                        '$phone',
                        '$password',
                        '$address1',
                        '$address2',
                        '$address3',
                        '$country',
                    ];
                    break;
            default:
                # code...
                break;
        }
        return response()->json($list);
    }

    public function arrayGroup()
    {
        return  [
            'order_success_to_admin' => trans('email.email_action.order_success_to_admin'),
            'order_success_to_customer' => trans('email.email_action.order_success_to_cutomer'),
            'forgot_password' => trans('email.email_action.forgot_password'),
            'customer_verify' => trans('email.email_action.customer_verify'),
            'welcome_customer' => trans('email.email_action.welcome_customer'),
            'contact_to_admin' => trans('email.email_action.contact_to_admin'),
            'other' => trans('email.email_action.other'),
        ];
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminEmailTemplate::getEmailTemplateAdmin($id);
    }
}
