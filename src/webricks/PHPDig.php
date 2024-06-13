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
    
    /**
     * The dns server.
     * @var string
     */
    private $server = null;
    
    /**
     * Trace option flag.
     * @var bool
     */
    private $trace = false;
    
    public function query($query, $type = null) {
        $data = $this->exec([$query, $type]);
        if($this->trace)
            return $data;
        
        return $this->parseOutput($data);
    }
    
    /**
     * Get the dns server.
     * @return string
     */
    public function getServer() {
        return $this->server;
    }

    /**
     * Set the dns server.
     * @param string $server
     * @return void
     */
    public function setServer($server): void {
        $this->server = $server;
    }
    
    /**
     * Get the trace flag.
     * @return bool
     */
    public function getTrace(): bool {
        return $this->trace;
    }

    /**
     * Set the trace flag.
     * @param booelan $trace
     * @return void
     */
    public function setTrace(bool $trace): void {
        $this->trace = $trace;
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
            
            if(substr($ltrimed, 0, 2) == ';;')
                continue;
            
            $parts = preg_split('/[\s]+/', $line, 5);
            if(count($parts) !== 5)
                var_dump($line);
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
        
        if($this->trace)
            $baseArgs[] = '+trace';
        
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