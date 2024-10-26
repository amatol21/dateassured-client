<?php

class SignatureGenerator
{
    private string $_secretKey;

    public function __construct(string $secretKey)
    {
        $this->_secretKey = $secretKey;
    }

    public function createAuthMessage(int $userId): array
    {
        $time = time();
        return [
            'time' => $time,
            'userId' => $userId,
            'signature' => md5($time . $userId . $this->_secretKey)
        ];
    }
}