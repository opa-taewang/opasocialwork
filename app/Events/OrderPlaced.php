<?php

namespace App\Events;

use App\Models\OpaSocial\Order;


class OrderPlaced
{
    use \Illuminate\Foundation\Events\Dispatchable;
    use \Illuminate\Broadcasting\InteractsWithSockets;
    use \Illuminate\Queue\SerializesModels;

    public $order = NULL;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
