<? require_once('Class_BD.php'); 
   
   $BD = new Class_BD();
   $Data = $BD->select('people', Array('*'), '', false, false); 

?><thead>
	   <tr>
		<th>№</th>
		<th>Фамилия</th>
		<th>Имя</th>
		<th>Отчество</th>
		<th></th>
	   </tr>
	</thead>
	<tbody>
		<? foreach ($Data as $item) {?>
		<tr>
			<td><?=$item['id'];?></td>
			<td><p class="famtext" id="famtext<?=$item['id']?>"><?=$item['fam'];?></p>
			<span class="famedittext disabled" id="famedittext<?=$item['id'];?>"><input id="famedit<?=$item['id']?>" value="<?=$item['fam']?>"/></span></td>
			<td><p class="fnametext" id="fnametext<?=$item['id']?>"><?=$item['fname'];?></p>
			<span class="fnameedittext disabled" id="fnameedittext<?=$item['id'];?>"><input id="fnameedit<?=$item['id']?>" value="<?=$item['fname']?>"/></span></td>							
			</td>
			<td><p class="snametext" id="snametext<?=$item['id']?>"><?=$item['sname'];?></p>
			<span class="snameedittext disabled" id="snameedittext<?=$item['id'];?>"><input id="snameedit<?=$item['id']?>" value="<?=$item['sname']?>"/></span></td>
			</td>
			<td><input type="checkbox" id="<?=$item["id"]?>" name="<?=$item['id'];?>" class="checkbox"></td>
		</tr>
		<? } ?>
	</tbody>