<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateZohoDealAndAccountRequest;
use App\Services\ZohoService;
use Illuminate\Http\JsonResponse;

class ZohoController extends Controller
{
    public ZohoService $zohoService;

    public function __construct(ZohoService $zohoService)
    {
        $this->zohoService = $zohoService;
    }

    public function __invoke(CreateZohoDealAndAccountRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {

            $accountId = $this->handleAccountCreation($data);
            $dealId = $this->handleDealCreation($data, $accountId);


            return response()->json([
                'message' => 'Deal and Account successfully created in Zoho CRM',
                'account_id' => $accountId,
                'deal_id' => $dealId
            ]);

        } catch (\Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 500);
        }
    }


    private function handleAccountCreation(array $data)
    {
        $accountData = [
            [
                'Account_Name' => $data['account_name'],
                'Website' => $data['account_website'] ?? '',
                'Phone' => $data['account_phone'] ?? '',
            ]
        ];

        $accountResponse = $this->zohoService->createAccount($accountData);

        if (!isset($accountResponse['data'][0]['details']['id'])) {
            throw new \RuntimeException('Failed to create account in Zoho CRM');
        }

        return $accountResponse['data'][0]['details']['id'];
    }

    private function handleDealCreation(array $data, $accountId)
    {
        $dealData = [
            [
                'Deal_Name' => $data['deal_name'],
                'Stage' => $data['deal_stage'],
                'Account_Name' => ['id' => $accountId],
            ]
        ];
        $dealResponse = $this->zohoService->createDeal($dealData);

        if (!isset($dealResponse['data'][0]['details']['id'])) {
            throw new \RuntimeException('Failed to create deal in Zoho CRM');
        }

        return $dealResponse['data'][0]['details']['id'];

    }
}
