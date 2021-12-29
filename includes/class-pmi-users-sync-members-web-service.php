<?php

/**
 * The file that defines the PMI DEPService web service to retrieve the list of PMI Chapter members directly from PMI
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.1.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */


/**
 * Represents the PMI DEPService web service called to retrieve the list of members of the PMI Chapter in CSV format
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_Members_Web_Service
{
    private int $members_count = 0;
    private string $csv_extract = '';

    private string $username;
    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->call($this->username, $this->password);
    }

    /**
     * Loads PMI members list of a PMI Chapter using DEPService web service
     * 
     * @link https://components.pmi.org/UI/HelpDocs/HowToUseCS.pdf Component System User Guide
     * @return stdClass the SOAP object returned by the web service
     * 
     * SOAP output
     * 
     * stdClass Object
     * (
     * [Message] => 
     * [ServiceErrorMessageId] => 
     * [Success] => true
     * [ExtractFile] => { CSV-like list of members }
     * CSV Header
     * "ID","FullName","Prefix","FirstName","LastName","Suffix","Designation","NickName","NameType","WTitle","WCompany","WAddress1","WAddress2","WAddress3","WCity","WState","WZip","WCountry","WPhone","WPhoneExt","WFax","WEmail","HTitle","HCompany","HAddress1","HAddress2","HAddress3","HCity","HState","HZip","HCountry","HPhone","HPhoneExt","HFax","HEmail","Title","Company","Address1","Address2","Address3","City","State","Zip","Country","Phone","PhoneExt","Fax","Email","PrefMailAddr","PMPNumber","PMPDate","PMIJoinDate","PMIExpirationDate","Chapters","ChapterCount","SIGs","SIGsCount","IndustryCodes","IndustryCodeCount","OccupationCodes","OccupationCount","JoinDate","ExpirationDate","MemberClass","MemberGroup","MbrGroup","Directory","MailingList","RecordEdited","SortKey","PrefPhone","DataDate","PMIAutoRenewStatus","ChapterAutoRenewStatus","MobileTelephone"
     * 
     * [LastRun] => 2021-12-28T05:23:26.707+00:00
     * [MemberCount] => 420
     * )
     * 
     */
    public function call()
    {
        $endpoint_url = 'https://svc.pmi.org/DEPServices/services/DEP.svc';
        $method_name = 'GetMemberExtractReport';
        $service_name = 'DEPService';
        $method_namespace = 'http://svc.pmi.org/2011/01/15';
        $auth_namespace = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

        $token = new stdClass;
        $token->Username = new SoapVar($this->username, XSD_STRING, null, null, null, $auth_namespace);
        $token->Password = new SoapVar($this->password, XSD_STRING, null, null, null, $auth_namespace);

        $wsec = new stdClass;
        $wsec->UsernameToken = new SoapVar($token, SOAP_ENC_OBJECT, null, null, null, $auth_namespace);
        $auth_header = new SoapHeader($auth_namespace, 'Security', $wsec, true);

        $array_options = array(
            'soap_version' => SOAP_1_1,
            'trace' => 1, // DEBUG
            'exceptions' => true, // DEBUG
            'cache_wsdl' => WSDL_CACHE_NONE,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'location' => $endpoint_url,
            'uri' => $method_namespace
        );

        $soap_client = new SoapClient(NULL, $array_options);
        $soap_client->__setSoapHeaders(array($auth_header));

        try {
            //Build method call
            $arguments = array();
            $action = array(
                'soapaction' => "$method_namespace/$service_name/$method_name",
                'uri' => $method_namespace
            );
            $result = $soap_client->__soapCall($method_name, $arguments, $action);
            $this->members_count = intval($result->MemberCount);
            $this->csv_extract = $result->ExtractFile;
            return $result;
        } catch (SoapFault $fault) {
            throw $fault;
        }
    }

    public function get_members_count()
    {
        return $this->members_count;
    }

    public function get_csv_extract(): string
    {
        return $this->csv_extract;
    }
}
