<?php
namespace webricks\PHPDig;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


/**
 * PHPDig
 *
 * @author j.aloy :: yoonic :: Linked applications
 */
class PHPDig {
    
    const BASE_ARGS = ['dig', '+noall', '+answer'];
    
    private $server = null;
    
    public function query($query, $type = null) {
        $data = $this->exec([$query, $type]);
        return $this->parseOutput($data);
    }
    
    public function getServer() {
        return $this->server;
    }

    public function setServer($server): void {
        $this->server = $server;
    }
    
    protected function parseOutput($out) {
        $trimmed = trim($out);
        if(strlen($out) <= 0)
            return [];

        $lines = explode("\n", $out);
        $ret = [];
        foreach($lines as $line) {
            $ltrimed = trim($line);
            if(strlen($ltrimed) <= 0)
                continue;
            
            $parts = preg_split('/\s+/', $line);

            $ret[] = array(
                'name' => $parts[0], 'ttl' => $parts[1], 'class' => $parts[2], 'type' => $parts[3], 'record' => $parts[4]
            );
        }
        
        return $ret;
    }


    protected function exec($args) {
        $baseArgs = self::BASE_ARGS;
        if($this->server != null)
            $baseArgs[] = '@' . $this->server;
        
        $execArgs = array_merge($baseArgs, $args);
        $p = new Process($execArgs);
        try {
            $p->mustRun();
            return $p->getOutput();
        } catch (ProcessFailedException $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }
}