@component('mail::layout')
@slot('header')
   {{--Empty header--}}
@endslot
<table>
	<tr>
		<td colspan="5" style="text-align: center;"><b>Seller Package Changes</b></td>
	</tr>
	<tr>
		<td colspan="5"><hr/></td>
	</tr>
	<tr>
		<th style="max-width:100%;white-space:nowrap;text-align: center;border-style: none;border-top: none;border-bottom: none; padding: 10px">API</th>
		<th style="max-width:100%;white-space:nowrap;text-align: center;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">Package ID</th>
		<th style="max-width:100%;white-space:nowrap;text-align: center;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">Package Name</th>
		<th style="max-width:100%;white-space:nowrap;text-align: center;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">Change</th>
		<th style="max-width:100%;white-space:nowrap;text-align: center;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">Action</th>
	</tr>
	<tr>
		<td colspan="5"><hr/></td>
	</tr>
	@foreach($table as $row)
		<tr>
			<td style="max-width:100%;white-space:nowrap;border-style: none;border-top: none;border-bottom: none; padding: 10px">{{$row['api_id'].'::'.$row['api_name']}}</td>
			<td style="max-width:100%;white-space:nowrap;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">{{$row['package_id']}}</td>
			<td style="max-width:100%;white-space:nowrap;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">{{$row['package_name']}}</td>
			<td style="max-width:100%;white-space:nowrap;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">{{$row['reason']}}</td>
			<td style="max-width:100%;white-space:nowrap;border-style: none;border-top: none;border-bottom: none;border-left: none; padding: 10px">{{$row['action']}}</td>
		</tr>
		<tr>
			<td colspan="5"><hr/></td>
		</tr>
	@endforeach
</table>
@slot('footer')
    {{--Empty footer--}}
@endslot
@endcomponent