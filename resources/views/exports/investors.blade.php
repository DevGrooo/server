<table>
    <thead>
    <tr>
        <th>DBCODE</th>
        <th>CUSTODYCD</th>
        <th>ACCTYPE</th>
        <th>FULLNAME</th>
        <th>IDTYPE</th>
        <th>IDCODE</th>
        <th>IDDATE</th>
        <th>IDPLACE</th>
        <th>BIRTHDATE</th>
        <th>SEX</th>
        <th>COUNTRY</th>
        <th>TAXNO</th>
        <th>REGADDRESS</th>
        <th>ADDRESS</th>
        <th>PHONE</th>
        <th>FAX</th>
        <th>EMAIL</th>
        <th>INVESTTYPE</th>
        <th>CUSTTYPE</th>
        <th>GRINVESTOR</th>
        <th>BANKACC</th>
        <th>BANKCODE</th>
        <th>CITYBANK</th>
        <th>DESCRIPTION</th>
        <th>REFNAME1</th>
        <th>REFPOST1</th>
        <th>REFIDCODE1</th>
        <th>REFIDDATE1</th>
        <th>REFIDPLACE1</th>
        <th>REFCOUNTRY1</th>
        <th>REFMOBILE1</th>
        <th>REFADDRESS1</th>
        <th>AUTHNAME</th>
        <th>AUTHIDCODE</th>
        <th>AUTHIDDATE</th>
        <th>AUTHIDPLACE</th>
        <th>AUTHPHONE</th>
        <th>AUTHADDRESS</th>
        <th>AUTHEFDATE</th>
        <th>AUTHEXDATE</th>
        <th>LINKAUTH</th>
        <th>RECODE</th>
        <th>SYMBOLS</th>
        <th>FATCA1</th>
        <th>FATCA2</th>
        <th>FATCA3</th>
        <th>FATCA4</th>
        <th>FATCA5</th>
        <th>FATCA6</th>
        <th>FATCA7</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $row)
        <tr>
            <td>{{ $row['fund_distributor_code'] }}</td>
            <td>{{ $row['trading_account_number'] }}</td>
            <td>{{ ($row['trading_account_type'] == 1) ? 'TT' : 'GT' }}</td>
            <td>{{ $row['name'] }}</td>
            <td>{{ $row['id_type_code'] }}</td>
            <td>{{ $row['id_number'] }}</td>
            <td>{{ $row['id_issuing_date'] }}</td>
            <td>{{ $row['id_issuing_place'] }}</td>
            <td>{{ $row['birthday'] }}</td>
            <td>{{ $row['gender'] }}</td>
            <td>{{ $row['country_id'] }}</td>
            <td>{{ $row['tax_id'] }}</td>
            <td>{{ $row['permanent_address'] }}</td>
            <td>{{ $row['current_address'] }}</td>
            <td>{{ $row['phone'] }}</td>
            <td>{{ $row['fax'] }}</td>
            <td>{{ $row['email'] }}</td>
            <td>{{ $row['invest_type'] == 1 ? 'TT' : 'CN' }}</td>
            <td>{{ $row['scale_type'] == 1 ? 'CN' : 'TC' }}</td>
            <td>{{ $row['zone_type'] == 1 ? 'TN' : 'NN' }}</td>
            <td>{{ $row['account_number'] }}</td>
            <td>{{ $row['bank']['code'] }}</td>
            <td>{{ $row['branch'] }}</td>
            <td>{{ $row['description'] }}</td>
            <td>{{ $row['re_fullname'] }}</td>
            <td>{{ $row['re_position'] }}</td>
            <td>{{ $row['re_id_number'] }}</td>
            <td>{{ $row['re_id_issuing_date'] }}</td>
            <td>{{ $row['re_id_issuing_place'] }}</td>
            <td>{{ $row['re_country_name'] }}</td>
            <td>{{ $row['re_phone'] }}</td>
            <td>{{ $row['re_address'] }}</td>
            <td>{{ $row['au_fullname'] }}</td>
            <td>{{ $row['au_id_number'] }}</td>
            <td>{{ $row['au_id_issuing_date'] }}</td>
            <td>{{ $row['au_id_issuing_place'] }}</td>
            <td>{{ $row['au_phone'] }}</td>
            <td>{{ $row['au_address'] }}</td>
            <td>{{ $row['au_start_date'] }}</td>
            <td>{{ $row['au_end_date'] }}</td>
            <td>{{ $row['fatca_link_auth'] }}</td>
            <td>{{ $row['fatca_recode'] }}</td>
            <td>{{ implode(',', $row['fatca_funds']) }}</td>
            <td>{{ $row['fatca1'] == 1 ? 'Y' : 'N' }}</td>
            <td>{{ $row['fatca2'] == 1 ? 'Y' : 'N' }}</td>
            <td>{{ $row['fatca3'] == 1 ? 'Y' : 'N' }}</td>
            <td>{{ $row['fatca4'] == 1 ? 'Y' : 'N' }}</td>
            <td>{{ $row['fatca5'] == 1 ? 'Y' : 'N' }}</td>
            <td>{{ $row['fatca6'] == 1 ? 'Y' : 'N' }}</td>
            <td>{{ $row['fatca7'] == 1 ? 'Y' : 'N' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>