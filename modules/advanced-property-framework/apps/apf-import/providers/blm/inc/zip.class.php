<?php
/**************************************************************************************************
 *  File Defination 
 *  - Zip compression
 -------------------------------------------------------------------
    ABOUT THIS  3RD PARTY LIB.
 --------------------------------------------------------------------
 *  Downlaoed from : www.phpclasses.org
 *  http://www.phpclasses.org/browse/file/3631.html 
 -------------------------------------------------------------------  
 *  Run on PHP versions 4 and 5
 -------------------------------------------------------------------
 *  Apprain : Content Management Framework <http://www.apprain.com/>
 *  Download link: http://www.apprain.com/download
 *  Docs link: http://www.apprain.com/docs
 -------------------------------------------------------------------
 *  License text http://www.opensource.org/licenses/mit-license.php 
 *  About MIT license <http://en.wikipedia.org/wiki/MIT_License/>
*************************************************************************************************/
class zipfile
{
    /**
     * Array to store compressed data
     *
     * @var  array    $datasec
     */
    var $datasec      = array();
 
    /**
     * Central directory
     *
     * @var  array    $ctrl_dir
     */
    var $ctrl_dir     = array();
 
    /**
     * End of central directory record
     *
     * @var  string   $eof_ctrl_dir
     */
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
 
    /**
     * Last offset position
     *
     * @var  integer  $old_offset
     */
    var $old_offset   = 0;
 
 
    /**
     * Converts an Unix timestamp to a four byte DOS date and time format (date
     * in high two bytes, time in low two bytes allowing magnitude comparison).
     *
     * @param  integer  the current Unix timestamp
     *
     * @return integer  the current date in a four byte DOS format
     *
     * @access private
     */
    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
 
        if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } // end if
 
        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method
 
 
    /**
     * Adds "file" to archive
     *
     * @param  string   file contents
     * @param  string   name of the file in the archive (may contains the path)
     * @param  integer  the current timestamp
     *
     * @access public
     */
    function addFile($data, $name, $time = 0)
    {
        $name     = str_replace('\\', '/', $name);
 
        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');
 
        $fr   = "\x50\x4b\x03\x04";
        $fr   .= "\x14\x00";            // ver needed to extract
        $fr   .= "\x00\x00";            // gen purpose bit flag
        $fr   .= "\x08\x00";            // compression method
        $fr   .= $hexdtime;             // last mod time and date
 
        // "local file header" segment
        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data);
        $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
        $c_len   = strlen($zdata);
        $fr      .= pack('V', $crc);             // crc32
        $fr      .= pack('V', $c_len);           // compressed filesize
        $fr      .= pack('V', $unc_len);         // uncompressed filesize
        $fr      .= pack('v', strlen($name));    // length of filename
        $fr      .= pack('v', 0);                // extra field length
        $fr      .= $name;
 
        // "file data" segment
        $fr .= $zdata;
 
        // "data descriptor" segment (optional but necessary if archive is not
        // served as file)
        $fr .= pack('V', $crc);                 // crc32
        $fr .= pack('V', $c_len);               // compressed filesize
        $fr .= pack('V', $unc_len);             // uncompressed filesize
 
        // add this entry to array
        $this -> datasec[] = $fr;
 
        // now add to central directory record
        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";                // version made by
        $cdrec .= "\x14\x00";                // version needed to extract
        $cdrec .= "\x00\x00";                // gen purpose bit flag
        $cdrec .= "\x08\x00";                // compression method
        $cdrec .= $hexdtime;                 // last mod time & date
        $cdrec .= pack('V', $crc);           // crc32
        $cdrec .= pack('V', $c_len);         // compressed filesize
        $cdrec .= pack('V', $unc_len);       // uncompressed filesize
        $cdrec .= pack('v', strlen($name) ); // length of filename
        $cdrec .= pack('v', 0 );             // extra field length
        $cdrec .= pack('v', 0 );             // file comment length
        $cdrec .= pack('v', 0 );             // disk number start
        $cdrec .= pack('v', 0 );             // internal file attributes
        $cdrec .= pack('V', 32 );            // external file attributes - 'archive' bit set
 
        $cdrec .= pack('V', $this -> old_offset ); // relative offset of local header
        $this -> old_offset += strlen($fr);
 
        $cdrec .= $name;
 
        // optional extra field, file comment goes here
        // save to central directory
        $this -> ctrl_dir[] = $cdrec;
    } // end of the 'addFile()' method
 
 
    /**
     * Dumps out file
     *
     * @return  string  the zipped file
     *
     * @access public
     */
    function file()
    {
        $data    = implode('', $this -> datasec);
        $ctrldir = implode('', $this -> ctrl_dir);
 
        return
            $data .
            $ctrldir .
            $this -> eof_ctrl_dir .
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries "on this disk"
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries overall
            pack('V', strlen($ctrldir)) .           // size of central dir
            pack('V', strlen($data)) .              // offset to start of central dir
            "\x00\x00";                             // .zip file comment length
    } // end of the 'file()' method
     
 
    /**
     * A Wrapper of original addFile Function
     *
     *
     * @param array An Array of files with relative/absolute path to be added in Zip File
     *
     * @access public
     */
    function addFiles($files /*Only Pass Array*/)
    {
        foreach($files as $file)
        {
            if (is_file($file)) //directory check
            {
                $data = implode("",file($file));
                $this->addFile($data,$file);
            }
             
        }
    }
     
    /**
     * A Wrapper of original file Function
     *
     *
     * @param string Output file name
     *
     * @access public
     */
    function output($file = NULL)
    {
        if( isset($file))
        {
            $fp=fopen($file,"w");
            fwrite($fp,$this->file());
            fclose($fp);
        }
        else
        {
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="downloaded.zip"');
            echo $this->file();
        }
    }
 
    function read_zip($name)
    {
        // Clear current file
        $this->datasec = array();
 
        // File information
        $this->name = $name;
        $this->mtime = filemtime($name);
        $this->size = filesize($name);
 
        // Read file
        $fh = fopen($name, "rb");
        $filedata = fread($fh, $this->size);
        fclose($fh);
 
        // Break into sections
        $filesecta = explode("\x50\x4b\x05\x06", $filedata);
 
        // ZIP Comment
        $unpackeda = unpack('x16/v1length', $filesecta[1]);
        $this->comment = substr($filesecta[1], 18, $unpackeda['length']);
        $this->comment = str_replace(array("\r\n", "\r"), "\n", $this->comment); // CR + LF and CR -> LF
 
        // Cut entries from the central directory
        $filesecta = explode("\x50\x4b\x01\x02", $filedata);
        $filesecta = explode("\x50\x4b\x03\x04", $filesecta[0]);
        array_shift($filesecta); // Removes empty entry/signature
 
        foreach($filesecta as $filedata)
        {
            // CRC:crc, FD:file date, FT: file time, CM: compression method, GPF: general purpose flag, VN: version needed, CS: compressed size, UCS: uncompressed size, FNL: filename length
            $entrya = array();
            $entrya['error'] = "";
 
            $unpackeda = unpack("v1version/v1general_purpose/v1compress_method/v1file_time/v1file_date/V1crc/V1size_compressed/V1size_uncompressed/v1filename_length", $filedata);
 
            // Check for encryption
            $isencrypted = (($unpackeda['general_purpose'] & 0x0001) ? true : false);
 
            // Check for value block after compressed data
            if($unpackeda['general_purpose'] & 0x0008)
            {
                $unpackeda2 = unpack("V1crc/V1size_compressed/V1size_uncompressed", substr($filedata, -12));
 
                $unpackeda['crc'] = $unpackeda2['crc'];
                $unpackeda['size_compressed'] = $unpackeda2['size_uncompressed'];
                $unpackeda['size_uncompressed'] = $unpackeda2['size_uncompressed'];
 
                unset($unpackeda2);
            }
 
            $entrya['name'] = substr($filedata, 26, $unpackeda['filename_length']);
 
            if(substr($entrya['name'], -1) == "/") // skip directories
            {
                continue;
            }
 
            $entrya['dir'] = dirname($entrya['name']);
            $entrya['dir'] = ($entrya['dir'] == "." ? "" : $entrya['dir']);
            $entrya['name'] = basename($entrya['name']);
 
 
            $filedata = substr($filedata, 26 + $unpackeda['filename_length']);
 
            if(strlen($filedata) != $unpackeda['size_compressed'])
            {
                $entrya['error'] = "Compressed size is not equal to the value given in header.";
            }
 
            if($isencrypted)
            {
                $entrya['error'] = "Encryption is not supported.";
            }
            else
            {
                switch($unpackeda['compress_method'])
                {
                    case 0: // Stored
                        // Not compressed, continue
                    break;
                    case 8: // Deflated
                        $filedata = gzinflate($filedata);
                    break;
                    case 12: // BZIP2
                        if(!extension_loaded("bz2"))
                        {
                            @dl((strtolower(substr(PHP_OS, 0, 3)) == "win") ? "php_bz2.dll" : "bz2.so");
                        }
 
                        if(extension_loaded("bz2"))
                        {
                            $filedata = bzdecompress($filedata);
                        }
                        else
                        {
                            $entrya['error'] = "Required BZIP2 Extension not available.";
                        }
                    break;
                    default:
                        $entrya['error'] = "Compression method ({$unpackeda['compress_method']}) not supported.";
                }
 
                if(!$entrya['error'])
                {
                    if($filedata === false)
                    {
                        $entrya['error'] = "Decompression failed.";
                    }
                    elseif(strlen($filedata) != $unpackeda['size_uncompressed'])
                    {
                        $entrya['error'] = "File size is not equal to the value given in header.";
                    }
                    elseif(crc32($filedata) != $unpackeda['crc'])
                    {
                        $entrya['error'] = "CRC32 checksum is not equal to the value given in header.";
                    }
                }
 
                $entrya['filemtime'] = mktime(($unpackeda['file_time']  & 0xf800) >> 11,($unpackeda['file_time']  & 0x07e0) >>  5, ($unpackeda['file_time']  & 0x001f) <<  1, ($unpackeda['file_date']  & 0x01e0) >>  5, ($unpackeda['file_date']  & 0x001f), (($unpackeda['file_date'] & 0xfe00) >>  9) + 1980);
                $entrya['data'] = $filedata;
            }
 
            $this->files[] = $entrya;
        }
 
        return $this->files;
    }
} // end of the 'zipfile' class
?>