<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use App\Http\Resources\TestResource;
use App\Http\Controllers\Traits\DatatableTrait;
use App\Jobs\ProcessStatementJob;
use App\Jobs\ProcessStatementLineJob;
use App\Models\StatementLine;
use App\Models\TradingSession;
use App\Services\Transactions\TradingSessionTransaction;
use Carbon\Carbon;
use Symfony\Component\Process\Process;

class TestController extends Controller
{
    use DatatableTrait;

    public function __construct()
    {
        
    }

    public function test()
    {
        $trading_sessions = TradingSession::where('limit_order_at', '<=', Carbon::now())->where('status', TradingSession::STATUS_ACTIVE)->get();        
        if ($trading_sessions) {
            $transaction = new TradingSessionTransaction();
            foreach ($trading_sessions as $trading_session) {
                $transaction->updateStatusTimeUp([
                    'trading_session_id' => $trading_session->id, 
                    'updated_by' => 0,
                ], true);
            }
        }
        die();
    }

    public function testJob()
    {
        $line = StatementLine::find(1);
        $job = (new ProcessStatementLineJob($line))->onConnection('sync')->onQueue('imports');
        dispatch($job);
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        return $this->extractInputFromRules($request, $rules);
    }

    public function index(Request $request)
    {
        return TestResource::collection(
            $this->getData(
                $request,
                Test::query(),
                ['name', 'email', 'age', 'address']
            )
        );
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:50',
            'age' => 'required|integer|min:1|max:200',
            'address' => 'required|string|max:255'
        ]);

        $test = Test::create($data);

        return new TestResource($test);
    }

    public function show($id)
    {
        $test = Test::findOrFail($id);

        return new TestResource($test);
    }

    public function update(Request $request, $id)
    {
        $test = Test::findOrFail($id);
        $data = $this->validate($request, [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:50',
            'age' => 'required|integer|min:1|max:200',
            'address' => 'required|string|max:255'
        ]);

        $test->update($data);
        return new TestResource($test);
    }
}
