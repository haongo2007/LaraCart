<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\EmailTemplate;
use App\Http\Resources\EmailTemplateCollection;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;
use Validator;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $dataSearch = request()->all();
        $data = EmailTemplate::getEmailTemplateListAdmin($dataSearch);
        return EmailTemplateCollection::collection($data)->additional(['message' => 'Successfully']);
    }

    /**
     * Groups view to choose variable detail
     * @return [type] [description]
     */
    public function groups()
    {
        $data = $this->arrayGroup();
        return response()->json(new JsonResponse($data), Response::HTTP_OK);
    }
    /**
     * Post create new item in admin
     * @return [type] [description]
    */
    public function store()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'group' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(new JsonResponse([], $validator->errors()), Response::HTTP_FORBIDDEN);
        }
        $dataInsert = [
            'name'     => $data['name'],
            'group'    => $data['group'],
            'content'     => $data['content'],
            'design'     => json_encode($data['design']),
            'status'   => !empty($data['status']) ? 1 : 0,
            'store_id' => $data['store_id'],
        ];
        EmailTemplate::createEmailTemplateAdmin($dataInsert);
        return response()->json(new JsonResponse(), Response::HTTP_OK);
    }

/**
 * update status
 */
    public function update($id)
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

    public function arrayGroup(){
        return [
            'order_success_to_admin' => [
                'title'   => trans('email.email_action.order_success_to_admin'),
                'required'  =>  [
                                    trans('email.order.mean_logo'),
                                    trans('email.order.mean_title'),
                                    trans('email.order.mean_orderID'),
                                    trans('email.order.mean_toname'),
                                    trans('email.order.mean_address'),
                                    trans('email.order.mean_email'),
                                    trans('email.order.mean_phone'),
                                    trans('email.order.mean_comment'),
                                    trans('email.order.mean_orderDetail'),
                                    trans('email.order.mean_subtotal'),
                                    trans('email.order.mean_shipping_fee'),
                                    trans('email.order.mean_discount'),
                                    trans('email.order.mean_total'),
                                    trans('email.order.mean_tax'),
                                    trans('email.order.mean_payment'),
                                    trans('email.order.mean_shipping'),
                                    trans('email.order.mean_currency'),
                                    trans('email.order.mean_invoice_date'),
                                ]
            ],
            'order_success_to_customer' => [
                'title'   => trans('email.email_action.order_success_to_cutomer'),
                'required'  =>  [
                                    trans('email.order.mean_logo'),
                                    trans('email.order.mean_title'),
                                    trans('email.order.mean_orderID'),
                                    trans('email.order.mean_toname'),
                                    trans('email.order.mean_address'),
                                    trans('email.order.mean_email'),
                                    trans('email.order.mean_phone'),
                                    trans('email.order.mean_comment'),
                                    trans('email.order.mean_orderDetail'),
                                    trans('email.order.mean_subtotal'),
                                    trans('email.order.mean_shipping_fee'),
                                    trans('email.order.mean_discount'),
                                    trans('email.order.mean_total'),
                                    trans('email.order.mean_tax'),
                                    trans('email.order.mean_payment'),
                                    trans('email.order.mean_shipping'),
                                    trans('email.order.mean_currency'),
                                    trans('email.order.mean_invoice_date'),
                                ]
            ],
            'forgot_password' => [
                'title'   => trans('email.email_action.forgot_password'),
                'required'  =>  [
                                    trans('email.forgot_password.mean_title'),
                                    trans('email.forgot_password.mean_reason_sednmail'),
                                    trans('email.forgot_password.mean_note_sendmail'),
                                    trans('email.forgot_password.mean_note_access_link'),
                                    trans('email.forgot_password.mean_reset_link'),
                                    trans('email.forgot_password.mean_reset_button'),
                                ]
            ],
            'customer_verify' => [
                'title'   => trans('email.email_action.customer_verify'),
                'required'  =>  [
                                    trans('email.customer_verify.mean_title'),
                                    trans('email.customer_verify.mean_reason_sednmail'),
                                    trans('email.customer_verify.mean_note_sendmail'),
                                    trans('email.customer_verify.mean_note_access_link'),
                                    trans('email.customer_verify.mean_url_verify'),
                                    trans('email.customer_verify.mean_button'),
                                ]
            ],
            'welcome_customer' => [
                'title'   => trans('email.email_action.welcome_customer'),
                'required'  =>  [
                                    trans('email.welcome_customer.mean_title'),
                                    trans('email.welcome_customer.mean_first_name'),
                                    trans('email.welcome_customer.mean_last_name'),
                                    trans('email.welcome_customer.mean_email'),
                                    trans('email.welcome_customer.mean_phone'),
                                    trans('email.welcome_customer.mean_password'),
                                    trans('email.welcome_customer.mean_address1'),
                                    trans('email.welcome_customer.mean_address2'),
                                    trans('email.welcome_customer.mean_address3'),
                                    trans('email.welcome_customer.mean_country'),
                                ]
            ],
            'contact_to_admin' => [
                'title'   => trans('email.email_action.contact_to_admin'),
                'required'  =>  [
                                    trans('email.contact_to_admin.mean_title'),
                                    trans('email.contact_to_admin.mean_name'),
                                    trans('email.contact_to_admin.mean_email'),
                                    trans('email.contact_to_admin.mean_phone'),
                                    trans('email.contact_to_admin.mean_content'),
                                ]
            ]
        ];
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id) {
        return AdminEmailTemplate::getEmailTemplateAdmin($id);
    }
}
