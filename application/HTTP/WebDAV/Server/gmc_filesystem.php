<?php // $Id$
/*
   +----------------------------------------------------------------------+
   | Copyright (c) 2002-2007 Christian Stocker, Hartmut Holzgraefe        |
   | All rights reserved                                                  |
   |                                                                      |
   | Redistribution and use in source and binary forms, with or without   |
   | modification, are permitted provided that the following conditions   |
   | are met:                                                             |
   |                                                                      |
   | 1. Redistributions of source code must retain the above copyright    |
   |    notice, this list of conditions and the following disclaimer.     |
   | 2. Redistributions in binary form must reproduce the above copyright |
   |    notice, this list of conditions and the following disclaimer in   |
   |    the documentation and/or other materials provided with the        |
   |    distribution.                                                     |
   | 3. The names of the authors may not be used to endorse or promote    |
   |    products derived from this software without specific prior        |
   |    written permission.                                               |
   |                                                                      |
   | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
   | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
   | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
   | FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE       |
   | COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,  |
   | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
   | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;     |
   | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER     |
   | CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT   |
   | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN    |
   | ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE      |
   | POSSIBILITY OF SUCH DAMAGE.                                          |
   +----------------------------------------------------------------------+
*/

require_once "engine/bo/CampaignBo.php";
require_once "engine/bo/DocumentBo.php";
//require_once "HTTP/WebDAV/Server.php";
//require_once "HTTP/WebDAV/Server.php";

require_once "HTTP/WebDAV/Server.php";
require_once "System.php";

/**
 * Filesystem access using WebDAV
 *
 * @access  public
 * @author  Hartmut Holzgraefe <hartmut@php.net>
 * @version @package-version@
 */
class HTTP_WebDAV_Server_GMCFilesystem extends HTTP_WebDAV_Server
{
	/**
	 * TODO to delete
	 *
	 * Root directory for WebDAV access
	 *
	 * Defaults to webserver document root (set by ServeRequest)
	 *
	 * @access private
	 * @var    string
	 */
	var $base = "/var/www/html";

	var $readConnection = null;

	var $internal = "/gerermacampagne/gmcdav";

	var $documentBase = "/";

	var $bankLabel = "Banque";
	var $quotationLabel = "Devis";
	var $invoiceLabel = "Factures";
	var $checkLabel = "Cheques";

    /**
     * Serve a webdav request
     *
     * @access public
     * @param  string
     */
    function ServeRequest($base = false)
    {
        // special treatment for litmus compliance test
        // reply on its identifier header
        // not needed for the test itself but eases debugging
        if (isset($this->_SERVER['HTTP_X_LITMUS'])) {
            error_log("Litmus test ".$this->_SERVER['HTTP_X_LITMUS']);
            header("X-Litmus-reply: ".$this->_SERVER['HTTP_X_LITMUS']);
        }

        $this->readConnection = openConnection();

         // let the base class do all the work
        parent::ServeRequest();
    }

    /**
     * No authentication is needed here
     *
     * @access private
     * @param  string  HTTP Authentication type (Basic, Digest, ...)
     * @param  string  Username
     * @param  string  Password
     * @return bool    true on successful authentication
     */
    function check_auth($type, $user, $pass)
    {
    	error_log("check_auth($type, $user, $pass)");

    	foreach($_SERVER as $key => $value) {
    		error_log("\t$key => $value");
    	}

    	return true;
    }


    /**
     * PROPFIND method handler
     *
     * @param  array  general parameter passing array
     * @param  array  return array for file properties
     * @return bool   true on success
     */
    function PROPFIND(&$options, &$files)
    {
        // get absolute fs path to requested resource
        $fspath = $this->base . $options["path"];

        error_log("PROPFIND($fspath)");

        // prepare property array
        $files["files"] = array();

        // store information for the requested path itself
        $files["files"][] = $this->fileinfo($options["path"]);

        // information for contained resources requested?
        if (!empty($options["depth"]) && $this->isDir($fspath)) {
            // make sure path ends with '/'
            $options["path"] = $this->_slashify($options["path"]);

            $children = $this->Dir($options["path"]);

            if (count($children)) {
	           	foreach($children as $child) {
	           		$files["files"][] = $this->fileinfo($options["path"].$child["name"], $child);
	           	}
            }

//                 // TODO recursion needed if "Depth: infinite"
        }

        // ok, all done
        return true;
    }

    /**
     * Get properties for a single file/resource
     *
     * @param  string  resource path
     * @return array   resource properties
     */
    function fileinfo($path, $fileinfo = null)
    {
    	error_log("fileinfo($path)");

        // create result array
        $info = array();
        // TODO remove slash append code when base clase is able to do it itself
        $info["path"]  = $this->isDir($path) ? $this->_slashify($path) : $path;
        $info["props"] = array();

        // no special beautified displayname here ...
        $info["props"][] = $this->mkprop("displayname", strtoupper($path));

        $fileinfo = $this->getFileInfo($path, $fileinfo);

        // creation and modification time
        $info["props"][] = $this->mkprop("creationdate", $fileinfo["creationdate"]);
        $info["props"][] = $this->mkprop("getlastmodified", $fileinfo["getlastmodified"]);

        // Microsoft extensions: last access time and 'hidden' status
        $info["props"][] = $this->mkprop("lastaccessed",    $fileinfo["lastaccessed"]);
//        $info["props"][] = $this->mkprop("ishidden", ('.' === substr(basename($fspath), 0, 1)));
        $info["props"][] = $this->mkprop("ishidden", false);

        // type and size (caller already made sure that path exists)
        if ($this->isDir($path)) {
            // directory (WebDAV collection)
            $info["props"][] = $this->mkprop("resourcetype", "collection");
            $info["props"][] = $this->mkprop("getcontenttype", "httpd/unix-directory");
        }
        else {
            // plain file (WebDAV resource)
            $info["props"][] = $this->mkprop("resourcetype", "");
			$info["props"][] = $this->mkprop("getcontenttype", $fileinfo["mimetype"]);
            $info["props"][] = $this->mkprop("getcontentlength", $fileinfo["size"]);
        }

        return $info;
    }

    /**
     * HEAD method handler
     *
     * @param  array  parameter passing array
     * @return bool   true on success
     */
    function HEAD(&$options)
    {
    	error_log("HEAD Webdav path : " . $options["path"]);

    	$internal = str_replace($this->internal, "", $options["path"]);

    	error_log("HEAD Webdav internal : " . $internal);

    	$parts = $this->slashSplit($internal);

    	if (count($parts) != 3) {
    		error_log("HEAD return false cause parts");
    		return false;
    	}

    	$campaignBo = new CampaignBo($this->readConnection);
    	$campaigns = $campaignBo->getCampaigns();

    	$currentCampaign = null;

    	foreach($campaigns as $campaign) {
    		if ($parts[0] == str_replace(" " , "_", $campaign["cam_webdav"])) {
    			$currentCampaign = $campaign;
    			break;
    		}
    	}

    	$documentId = explode("-", $parts[2]);
    	$documentId = $documentId[0];

    	error_log("HEAD document id : $documentId");

    	// Si pas numeric alors non
    	if (!is_numeric($documentId)) {
    		error_log("HEAD return false cause bad document id");
    		return false;
    	}

    	$documentBo = new DocumentBo($this->readConnection);
    	$document = $documentBo->getDocument($documentId);

    	// Le document n'existe pas ou n'impartient pas à la campagne courante
    	if (!$document || $document["doc_campaign_id"] != $campaign["cam_id"]) {
    		error_log("HEAD return false cause no document");
    		return false;
    	}

    	$fspath = $this->documentBase . $document["doc_path"];

    	if (!is_file($fspath)) {
    		error_log("$fspath not found on fs");
    		return false;
    	}

    	$options['mimetype'] = $document["doc_mime_type"];
    	$options['mtime'] = $document["doc_modification_date"];
    	$options['size'] = $document["doc_size"];

    	error_log("HEAD true");

        return true;
    }

    /**
     * GET method handler
     *
     * @param  array  parameter passing array
     * @return bool   true on success
     */
    function GET(&$options)
    {
    	error_log("GET Webdav path : " . $options["path"]);

    	$path = $options["path"];

        // is this a collection?
        if ($this->isDir($path)) {
            return $this->createHtmlDir($path, $options);
        }

        // the header output is the same as for HEAD
        if (!$this->HEAD($options)) {
	        error_log("GET false cause HEAD");
        	return false;
        }

        $internal = str_replace($this->internal, "", $options["path"]);
        $parts = $this->slashSplit($internal);

        if (count($parts) != 3) return false;

        $campaignBo = new CampaignBo($this->readConnection);
        $campaigns = $campaignBo->getCampaigns();

        $currentCampaign = null;

        foreach($campaigns as $campaign) {
        	if ($parts[0] == str_replace(" " , "_", $campaign["cam_webdav"])) {
        		$currentCampaign = $campaign;
        		break;
        	}
        }

        $documentId = explode("-", $parts[2]);
        $documentId = $documentId[0];

        // Si pas numeric alors non
        if (!is_numeric($documentId)) return false;

        $documentBo = new DocumentBo($this->readConnection);
        $document = $documentBo->getDocument($documentId);

        // Le document n'existe pas ou n'impartient pas à la campagne courante
        if (!$document || $document["doc_campaign_id"] != $campaign["cam_id"]) {
        	return false;
        }

        $fspath = $this->documentBase . $document["doc_path"];

    	if (!is_file($fspath)) {
			error_log("$fspath not found on fs");
        	return false;
        }

        // no need to check result here, it is handled by the base class
        $options['stream'] = fopen($fspath, "r");

        error_log("GET true");

        return true;
    }

    function Dir($fspath) {
    	error_log("Dir($fspath)");

    	$internal = str_replace($this->internal, "", $fspath);
    	$internal = str_replace($this->base, "", $internal);

    	error_log("=> Dir($internal)");

    	if ($this->IsRootDir($internal)) return $this->getRootDir();
    	if ($this->IsCampaignDir($internal)) return $this->getCampaignDir($internal);
    	if ($this->isBankDir($internal)) return $this->getBankDir($internal);
    	if ($this->isCheckDir($internal)) return $this->getCheckDir($internal);
    	if ($this->isQuotationDir($internal)) return $this->getQuotationDir($internal);
    	if ($this->isInvoiceDir($internal)) return $this->getInvoiceDir($internal);

    	return array();
    }

    function isDir($path) {
    	error_log("isDir($path)");

    	$internal = str_replace($this->internal, "", $path);
    	$internal = str_replace($this->base, "", $internal);

    	error_log("=> isDir($internal)");

    	if ($this->IsRootDir($internal)) return true;
    	if ($this->IsCampaignDir($internal)) return true;
    	if ($this->isBankDir($internal)) return true;
    	if ($this->isCheckDir($internal)) return true;
    	if ($this->isQuotationDir($internal)) return true;
    	if ($this->isInvoiceDir($internal)) return true;

    	error_log("=> false");

    	return false;
    }

    function IsRootDir($path) {
    	error_log("IsRootDir($path)");
    	return ($path == "/");
    }

    function slashSplit($path) {
		$parts = explode("/", $path);

		$finalParts = array();

		foreach($parts as $part) {
			if ($part) {
				$finalParts[] = $part;
			}
		}

		return $finalParts;
    }

    function IsCampaignDir($path) {
    	error_log("IsCampaignDir($path)");

    	$parts = $this->slashSplit($path);

    	error_log(" => Parts : ". count($parts));

    	if (count($parts) != 1) {
    		return false;
    	}

    	$campaignBo = new CampaignBo($this->readConnection);
    	$campaigns = $campaignBo->getCampaigns();

    	foreach($campaigns as $campaign) {
    		if ($parts[0] == str_replace(" " , "_", $campaign["cam_webdav"])) {
    			return true;
    		}
    	}

    	return false;
    }

    function isFunctionalDir($path, $functional) {
    	error_log("isFunctionalDir($path, $functional)");

    	$parts = $this->slashSplit($path);

    	error_log(" => Parts : ". count($parts));

    	if (count($parts) != 2) {
    		return false;
    	}

    	if ($parts[1] != $functional) {
    		return false;
    	}

    	if (!$this->IsCampaignDir("/" . $parts[0] . "/")) {
    		return false;
    	}

    	return true;
    }

    function isBankDir($path) {
    	error_log("isBankDir($path)");

    	return $this->isFunctionalDir($path, $this->bankLabel);
    }

    function isCheckDir($path) {
    	error_log("isCheckDir($path)");

    	return $this->isFunctionalDir($path, $this->checkLabel);
    }

    function isInvoiceDir($path) {
    	error_log("isInvoiceDir($path)");

    	return $this->isFunctionalDir($path, $this->invoiceLabel);
    }

    function isQuotationDir($path) {
    	error_log("isQuotationDir($path)");

    	return $this->isFunctionalDir($path, $this->quotationLabel);
    }

    function getRootDir() {
    	error_log("getRootDir()");

    	$dirs = array();

    	$campaignBo = new CampaignBo($this->readConnection);
    	$campaigns = $campaignBo->getCampaigns();

    	foreach($campaigns as $campaign) {
    		$dirs[] = array("name" => str_replace(" " , "_", $campaign["cam_webdav"]), "size" => 4096, "creationdate" => time(), "getlastmodified" => time(), "lastaccessed" => time());
    	}

    	return $dirs;
    }

    function getFileInfo($path, $fileinfo = null) {
    	if ($this->isDir($path)) {
	    	return array("name" => $path, "size" => 4096, "creationdate" => time(), "getlastmodified" => time(), "lastaccessed" => time());
    	}
    	else {
    		if ($fileinfo) return $fileinfo;

    		return array("name" => $path, "size" => 1, "creationdate" => time(), "getlastmodified" => time(), "lastaccessed" => time(), "mimetype" => "application_pdf");
    	}
    }

    function getCampaignDir($path) {
    	error_log("getCampaignDir($path)");

    	$dirs = array();
    	$dirs[] = array("name" => $this->checkLabel, "size" => 4096, "creationdate" => time(), "getlastmodified" => time(), "lastaccessed" => time());
    	$dirs[] = array("name" => $this->bankLabel, "size" => 4096, "creationdate" => time(), "getlastmodified" => time(), "lastaccessed" => time());
    	$dirs[] = array("name" => $this->quotationLabel, "size" => 4096, "creationdate" => time(), "getlastmodified" => time(), "lastaccessed" => time());
    	$dirs[] = array("name" => $this->invoiceLabel, "size" => 4096, "creationdate" => time(), "getlastmodified" => time(), "lastaccessed" => time());

    	return $dirs;
    }

    function getCheckDir($path) {
    	error_log("getCheckDir($path)");

    	$dirs = array();

    	$parts = $this->slashSplit($path);

    	$campaignBo = new CampaignBo($this->readConnection);
    	$campaigns = $campaignBo->getCampaigns();

    	$currentCampaign = null;

    	foreach($campaigns as $campaign) {
    		if ($parts[0] == str_replace(" " , "_", $campaign["cam_webdav"])) {
    			$currentCampaign = $campaign;
    			break;
    		}
    	}

    	$documentBo = new DocumentBo($this->readConnection);

    	$documents = $documentBo->getDocuments($campaign, array("doc_label" => "check"));

        foreach($documents as $document) {
    		$dirs[] = HTTP_WebDAV_Server_GMCFilesystem::toEntry($document);
    	}

    	return $dirs;
    }

    function getQuotationDir($path) {
    	error_log("getQuotationDir($path)");

    	$dirs = array();

    	$parts = $this->slashSplit($path);

    	$campaignBo = new CampaignBo($this->readConnection);
    	$campaigns = $campaignBo->getCampaigns();

    	$currentCampaign = null;

    	foreach($campaigns as $campaign) {
    		if ($parts[0] == str_replace(" " , "_", $campaign["cam_webdav"])) {
    			$currentCampaign = $campaign;
    			break;
    		}
    	}

    	$documentBo = new DocumentBo($this->readConnection);

    	$documents = $documentBo->getDocuments($campaign, array("doc_label" => "quotation"));

        foreach($documents as $document) {
    		$dirs[] = HTTP_WebDAV_Server_GMCFilesystem::toEntry($document);
    	}

    	return $dirs;
    }

    function getInvoiceDir($path) {
    	error_log("getInvoiceDir($path)");

    	$dirs = array();

    	$parts = $this->slashSplit($path);

    	$campaignBo = new CampaignBo($this->readConnection);
    	$campaigns = $campaignBo->getCampaigns();

    	$currentCampaign = null;

    	foreach($campaigns as $campaign) {
    		if ($parts[0] == str_replace(" " , "_", $campaign["cam_webdav"])) {
    			$currentCampaign = $campaign;
    			break;
    		}
    	}

    	$documentBo = new DocumentBo($this->readConnection);

    	$documents = $documentBo->getDocuments($campaign, array("doc_label" => "invoice"));

    	foreach($documents as $document) {
    		$dirs[] = HTTP_WebDAV_Server_GMCFilesystem::toEntry($document);
    	}

    	return $dirs;
    }

    static function toEntry($document) {
    	$entry = array();
    	$entry["name"] = $document["doc_id"] . "-" . $document["doc_name"];
    	$entry["size"] = $document["doc_size"];
    	$entry["mimetype"] = $document["doc_mime_type"];

    	$creationDate = new DateTime($document["doc_creation_date"]);
    	$modificationDate = new DateTime($document["doc_modification_date"]);

    	$entry["creationdate"] = $creationDate->getTimestamp();
    	$entry["getlastmodified"] = $modificationDate->getTimestamp();
    	$entry["lastaccessed"] = time();

		return $entry;
    }

    function getBankDir($path) {
    	return array();
    }

    /**
     * GET method handler for directories
     *
     * This is a very simple mod_index lookalike.
     * See RFC 2518, Section 8.4 on GET/HEAD for collections
     *
     * @param  string  directory path
     * @return void    function has to handle HTTP response itself
     */
    function createHtmlDir($path, &$options)
    {
    	error_log("createHtmlDir($path)");

        $path = $this->_slashify($options["path"]);
        if ($path != $options["path"]) {
            header("Location: ".$this->base_uri.$path);
            exit;
        }

        // fixed width directory column format
        $format = "%15s  %-19s  %-s\n";

        echo "<html><head><title>Index of ".htmlspecialchars($options['path'])."</title></head>\n";

        echo "<h1>Index of ".htmlspecialchars($options['path'])."</h1>\n";

        echo "<pre>";
        printf($format, "Size", "Last modified", "Filename");
        echo "<hr>";

        $entries = $this->Dir($path);
        foreach($entries as $entry) {
        	$fullpath = $path."/".$entry["name"];
        	$name     = htmlspecialchars($entry["name"]);
        	printf($format,
        			number_format($entry["size"]),
        			strftime("%Y-%m-%d %H:%M:%S", $entry["getlastmodified"]),
        			'<a href="' . $name . '">' . $name . '</a>');
        }

        echo "</pre>";

//         closedir($handle);

        echo "</html>\n";

        exit;
    }

    /**
     * PUT method handler
     *
     * @param  array  parameter passing array
     * @return bool   true on success
     */
    function PUT(&$options)
    {
        return "403 Forbidden";
    }

    /**
     * MKCOL method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function MKCOL($options)
    {
        return "403 Forbidden";
    }

    /**
     * DELETE method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function DELETE($options)
    {
        return "403 Forbidden";
    }

    /**
     * MOVE method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function MOVE($options)
    {
        return "403 Forbidden";
    }

    /**
     * COPY method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function COPY($options, $del=false)
    {
		return "403 Forbidden";
    }

    /**
     * PROPPATCH method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function PROPPATCH(&$options)
    {
        global $prefs, $tab;

        $msg  = "";
        $path = $options["path"];
        $dir  = dirname($path)."/";
        $base = basename($path);

        foreach ($options["props"] as $key => $prop) {
            if ($prop["ns"] == "DAV:") {
                $options["props"][$key]['status'] = "403 Forbidden";
            } else {
            }
        }

        return "";
    }

    /**
     * LOCK method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function LOCK(&$options)
    {
        return "403 Forbidden";
    }

    /**
     * UNLOCK method handler
     *
     * @param  array  general parameter passing array
     * @return bool   true on success
     */
    function UNLOCK(&$options)
    {
        return "403 Forbidden";
    }

    /**
     * checkLock() helper
     *
     * @param  string resource path to check for locks
     * @return bool   true on success
     */
    function checkLock($path)
    {
    	error_log("checklock($path)");

    	return false;
    }
}