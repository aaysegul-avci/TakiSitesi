<?php

declare(strict_types=1);

function generateOrderNumber(): string
{
    return 'FLR-' . date('YmdHis') . '-' . random_int(1000, 9999);
}

function orderCreateWithItems(PDO $pdo, array $orderData, array $items): int
{
    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare('
            INSERT INTO orders (
                user_id, order_number, customer_name, customer_email, customer_phone,
                address, total_amount, status
            ) VALUES (
                :user_id, :order_number, :customer_name, :customer_email, :customer_phone,
                :address, :total_amount, :status
            )
        ');

        $statement->execute([
            'user_id' => $orderData['user_id'] ?? null,
            'order_number' => $orderData['order_number'],
            'customer_name' => $orderData['customer_name'],
            'customer_email' => $orderData['customer_email'] ?? null,
            'customer_phone' => $orderData['customer_phone'] ?? null,
            'address' => $orderData['address'],
            'total_amount' => $orderData['total_amount'],
            'status' => $orderData['status'] ?? 'pending',
        ]);

        $orderId = (int) $pdo->lastInsertId();

        $itemStatement = $pdo->prepare('
            INSERT INTO order_items (
                order_id, product_id, product_name, unit_price, quantity, line_total
            ) VALUES (
                :order_id, :product_id, :product_name, :unit_price, :quantity, :line_total
            )
        ');

        foreach ($items as $item) {
            $itemStatement->execute([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'line_total' => $item['line_total'],
            ]);
        }

        $pdo->commit();
        return $orderId;
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function orderAll(PDO $pdo): array
{
    $statement = $pdo->prepare('
        SELECT id, order_number, user_id, customer_name, customer_email, customer_phone, total_amount, status, created_at, updated_at
        FROM orders
        ORDER BY id DESC
    ');
    $statement->execute();

    return $statement->fetchAll();
}

function orderStatusOptions(): array
{
    return [
        'pending' => 'Onay Bekliyor',
        'confirmed' => 'Onaylandi',
        'preparing' => 'Hazirlaniyor',
        'shipped' => 'Kargoya Verildi',
        'delivered' => 'Teslim Edildi',
        'cancelled' => 'Iptal Edildi',
    ];
}

function orderStatusLabel(string $status): string
{
    $options = orderStatusOptions();
    return $options[$status] ?? $status;
}

function orderUpdateStatus(PDO $pdo, int $orderId, string $status): void
{
    $allowedStatuses = array_keys(orderStatusOptions());

    if (!in_array($status, $allowedStatuses, true)) {
        throw new InvalidArgumentException('Gecersiz siparis durumu.');
    }

    $statement = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
    $statement->execute([
        'status' => $status,
        'id' => $orderId,
    ]);
}

function orderItemsByOrderId(PDO $pdo, int $orderId): array
{
    $statement = $pdo->prepare('
        SELECT product_id, product_name, unit_price, quantity, line_total
        FROM order_items
        WHERE order_id = :order_id
        ORDER BY id ASC
    ');
    $statement->execute(['order_id' => $orderId]);

    return $statement->fetchAll();
}

function orderFindByNumber(PDO $pdo, string $orderNumber): ?array
{
    $statement = $pdo->prepare('
        SELECT *
        FROM orders
        WHERE order_number = :order_number
        LIMIT 1
    ');
    $statement->execute(['order_number' => $orderNumber]);

    $order = $statement->fetch();
    if (!$order) {
        return null;
    }

    $order['items'] = orderItemsByOrderId($pdo, (int) $order['id']);
    return $order;
}

function orderListByUserId(PDO $pdo, int $userId): array
{
    $statement = $pdo->prepare('
        SELECT *
        FROM orders
        WHERE user_id = :user_id
        ORDER BY id DESC
    ');
    $statement->execute(['user_id' => $userId]);

    return $statement->fetchAll();
}
