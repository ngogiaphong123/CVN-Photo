<?php

namespace Services;

use App\Common\Enums\Token;
use App\Common\Error\AuthError;
use App\Core\Config;
use App\Exceptions\HttpException;
use App\Services\JwtService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JwtServiceTest extends TestCase
{
    private JwtService $jwtService;
    private MockObject $configMock;
    private static string $privateKey = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDazjV4yT/c2xqi46/NyaKq9RvLtkKOQdJCZf73uVNMCFANSUJm
ulWedxnlhLfMVRoeIF8UQHTab/cDc395oOF4jg3Gi69r+AGR7CgGB0ak5ZK7WlxG
HrUMyhkQCQa4jLfOAWb5ybDY2bys0+EZ4jcD/OvOzllJyjOd8Y3GcIeFnQIDAQAB
AoGAZ/4R0/Jyc9l+82QbrkbjFTWhnFRnlY0bDYvEfqCKQlzBD09+S6zL/KDGohtN
78OSwjbIeauY3ijQ0ccXEwuvnOdRwlG8HnzlJRuU8lF5lKfoinG3qP8VxYIGm0WJ
LtXd3X/SZzkWrumT0MzBonvB/YAqSkx3voXga9ATsM1+6lECQQD3whtx5/PLYhDZ
A2NN7W9uUrUMsR+8wKRC1CBCBiHgIfg6DKv4Lq3AWW5/7r9ANCNV9AfzpazbMFUM
8r+JFrWfAkEA4hWKydbabiWFNgowMw2+FVZtHmjRcXL5LnBz71Yo8bdfgyuQKjkm
/NRZDQPAVm1RQ+qS0qnKpl35TDuMwzbjQwJAJtM2Nc1hePevKDLNtwKEOcegM5L2
JEAT/Zz8SRxo5pSsL3yY3lWCSOg61rV1JvyEpQ2OyXGm+tpCGbtYpIT4hQJBAIjI
5c6WalOH6d+3LFBHOUEpqB1k54sPN8msCci9RUpxWp0+5xtUtszzIOmp4l2oCCni
K3C/f7dGVgWUZebaN4cCQQDbn/BgslM1I85RVNVmYLWIURfdWlrGLgMt74yXt1QY
wMhz8tWkHmHh9Tg8RPM9zhY3d2wQLNg/R1mE0Wl0s3SO
-----END RSA PRIVATE KEY-----';
    private static string $publicKey = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDazjV4yT/c2xqi46/NyaKq9RvL
tkKOQdJCZf73uVNMCFANSUJmulWedxnlhLfMVRoeIF8UQHTab/cDc395oOF4jg3G
i69r+AGR7CgGB0ak5ZK7WlxGHrUMyhkQCQa4jLfOAWb5ybDY2bys0+EZ4jcD/OvO
zllJyjOd8Y3GcIeFnQIDAQAB
-----END PUBLIC KEY-----';

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->jwtService = new JwtService($this->configMock);
    }

    public function testGenerateToken()
    {
        $this->configMock->expects($this->exactly(2))->method('get')->willReturn([
            'accessTokenTTL' => '1 days',
            'refreshTokenTTL' => '7 days',
            'privateKey' => self::$privateKey,
            'publicKey' => self::$publicKey,
        ]);
        $token = $this->jwtService->generateToken('userId', Token::ACCESS_TOKEN);
        $this->assertIsString($token);
    }

    /**
     * @throws \Exception
     */
    public function testVerifyTokenFailed()
    {
        $this->configMock->expects($this->atLeastOnce())->method('get')->with("jwt")->willReturn([
            'accessTokenTTL' => '1 days',
            'refreshTokenTTL' => '7 days',
            'privateKey' => self::$privateKey,
            'publicKey' => self::$publicKey,
        ]);
        $this->expectExceptionMessage(AuthError::ACCESS_TOKEN_EXPIRED->value);
        $this->expectException(HttpException::class);
        $this->jwtService->verifyToken("123");
    }

    /**
     * @throws \Exception
     */
    public function testVerifyTokenSuccess()
    {
        $this->configMock->expects($this->atLeastOnce())->method('get')->with("jwt")->willReturn([
            'accessTokenTTL' => '1 days',
            'refreshTokenTTL' => '7 days',
            'privateKey' => self::$privateKey,
            'publicKey' => self::$publicKey,
        ]);
        $token = $this->jwtService->generateToken('userId', Token::ACCESS_TOKEN);
        $this->assertIsString($token);
        $decoded = $this->jwtService->verifyToken($token);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('userId', $decoded);
    }
}
