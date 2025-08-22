

        <div style="border: 1px solid #ddd; border-radius: 5px; padding: 20px; background: #fff;">
               
<p style="font-size: 15px; margin-top: 5px;">
    Confidential | {{ now()->format('d/m/Y') }} | Quotation ID: {{ (string) $quotation->id ?? 'N/A' }}
</p>





           <div style="background-color:rgb(251, 194, 224); padding: 30px; display: flex; align-items: center; justify-content: center;">
    <img src="{{ asset('assets/time_logo.png') }}" alt="Time Logo" style="height: 29px; margin-right: 10px;">
    <span style="font-size: 30px; font-weight: bold; color: #000; line-height: 1;">CLOUD SERVICES</span>
</div style="margin: 0 auto; width: 800px;">

      <table style="width: 100%; border-collapse: collapse; font-size: 20px; margin-top: 0px;">
    <tr style="background:rgb(147, 145, 145); color: #fff;">
        <td style="padding: 20px; font-weight: bold; width: 100px;">Attention:</td>
        <td colspan="3" style="padding: 2px;">
            {{ $project->customer->name ?? 'N/A' }}
        </td>
    </tr>
               <table style="width: 100%; border-collapse: collapse; font-size: 18px; margin-top: 0px;">
    <tr>
        <td style="font-weight: bold; background: #f0f0f0;padding: 5px;">Contract Duration:</td>
        <td style="background: #fff; padding: 5px;">12 Months</td>
        <td style="font-weight: bold; background: #f0f0f0; padding: 5px;">Monthly Commitment<br>(Exclude SST):</td>
        <td style="background: #fff; padding: 5px;">RM0.00</td>
  
        
    </tr>
    <tr>
        <td style="font-weight: bold; background: #f0f0f0; padding: 5px;">One Time Charges <br>(Exclude SST):</td>
        <td style="background: #fff; padding: 5px;">RM{{ number_format($totalProfessionalCharges, 2) }}</td>
        <td style="font-weight: bold; background: #f0f0f0; padding: 5px;"> Annual Commitment:</td>
        <td style="background: #fff; padding: 5px;">RM0.00</td>
     </tr>     
</table>

 </table>

            
<div style="border: 1px solid #ccc; width: 100%;">
           <div style="background-color: #f0f0f0; padding: 5px; display: flex; align-items: center; justify-content: center;">
    <span style="font-size: 18px; font-weight: normal; color: #000; line-height: 1;">TOTAL CONTRACT VALUE (WITH SST)</span>
    
</div>

  <div style="background-color:rgb(255, 255, 255); padding: 5px; display: flex; align-items: center; justify-content: center;">
    <span style="font-size: 18px; font-weight: normal; color: #000; line-height: 1;">RM0.00</span>
    
</div>
    
</div>


            </table>

            <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: 18px; margin-top: 0px;">
                <thead>
                    <tr style="background: rgb(251, 194, 224);">
                        <th colspan="5" style="border: 1px solid #000; padding: 4px;">Summary of Quotation</th>
                    </tr>
                  <table style="width: 100%; border-collapse: collapse; font-size: 18px;">
    <tr style="background: rgb(147, 145, 145); color: #fff;">
        <th rowspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">Category</th>
        <th rowspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">One Time<br>Charges</th>
        <th colspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">Monthly Charges</th>
        <th rowspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">Total Charges</th>
    </tr>
    <tr style="background: rgb(147, 145, 145); color: #fff;">
        <th style="border: 1px solid #000; padding: 4px; font-weight: normal;">Region 1 (KL)</th>
        <th style="border: 1px solid #000; padding: 4px; font-weight: normal;">Region 2 (CJ)</th>
    </tr>


                </thead>
                <tbody>
                  <tr>
    <td style="border: 1px solid #000; padding: 4px;">Professional Services</td>
    <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($totalProfessionalCharges, 2) }}</td>
    <td style="border: 1px solid #000; padding: 4px; background: #000;">&nbsp;</td>
    <td style="border: 1px solid #000; padding: 4px; background: #000;">&nbsp;</td>
    <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($totalProfessionalCharges, 2) }}</td>
</tr>



                      <tr>
    <td style="border: 1px solid #000; padding: 4px;">Managed Services</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>
    <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format(collect($managedSummary)->sum('kl_price'), 2) }}</td>
    <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format(collect($managedSummary)->sum('cj_price'), 2) }}</td>
    <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($totalManagedCharges, 2) }}</td>
</tr>



                    <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Network</td>
                        <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($klTotal, 2) }}</td>
                        <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($cjTotal, 2) }}</td>
                        <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($klTotal + $cjTotal, 2) }}</td>
                    </tr>


                         <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Compute - ECS</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>


                         <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Compute - CCE</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00<td>
                    </tr>


                         <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Licenses</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>


                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Storage</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>

                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Backup</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>

                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">DR</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>

                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Cloud Security</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>


                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Additional Services - Data Protection</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>


                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Additional Services - Monitoring</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>

                    </tr>


                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Security Services</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>

                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Other Services (OTC)</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>


                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">Other Services (MRC)</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>


                      <tr>
                        <td style="border: 1px solid #000; padding: 4px;">3rd Party Services</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM0.00</td>
                    </tr>


                    
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <tr>
        <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            ONE TIME CHARGES TOTAL
        </td>
        <td style="background: #fff; padding: 5px; width: 120px; text-align: right; border: 1px solid #ccc;">
            RM 0.00
        </td>
    </tr>
    <tr>
        <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            MONTHLY TOTAL
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
            RM 0.00
        </td>
    </tr>
    <tr>
        <td style="background: #ccc; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            CONTRACT TOTAL
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
            RM 0.00
        </td>
    </tr>
    <tr>
        <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            ONE TIME CHARGE DISCOUNT
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
            RM 0.00
        </td>
    </tr>
    
    <tr>
        <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            SERVICE TAX (8%)
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
            RM 0.00
        </td>
    </tr>
    <tr>
        <td style="background: rgb(251, 194, 224); color: #000; padding: 5px; text-align: right; font-size: 16px;">
            FINAL TOTAL (Include Tax)
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
            RM 0.00
        </td>
    </tr>
</table>


<div style="margin-top: 0px; font-size: 12px; line-height: 1.5; border: 1px solid #000; padding: 10px; border-radius: 0px;">
    <h5 style="font-weight: normal; margin-bottom: 5px;">Terms and Conditions:</h5>
    <ol style="padding-left: 15px; margin: 0;">
        <li>The delivery lead time is subject to availability of our capacity, infrastructure and upon our acknowledgement of signed Service Order form.</li>
        <li>Price quoted only valid for customer stated within this quotation for a duration of 60 days.</li>
        <li>All prices quoted shall be subjected to
            <ul style="list-style-type: disc; margin-left: 15px; padding-left: 15px;">
                <li>Other charges and expenses incurred due to additional services not covered in the above quotation shall be charged based on actual amount incurred</li>
            </ul>
        </li>
        <li>All agreements for the provision of the services are for a fixed period and in the event of termination prior to the completion of the fixed period, 100% of the rental or regular charges for the remaining contract period shall be imposed.</li>
        <li>SLA is 99.95% Availability. No Performance SLA and Credit Guarantee is provided unless specifically mentioned.</li>
        <li>TIME will only be providing Infrastructure as a service only (IaaS). Operating System and Application will be self-managed by customer unless relevant service is subscribed.</li>
        <li>The price quoted does not include any Professional services and managed service beyond infrastructure level. If required, Scope of work and contract to be agreed before any work commence.</li>
        <li>All sums due are exclusive of the taxes of any nature including but not limited to service tax withholding taxes and any other taxes and all other government fees and charges assessed upon or with respect to the service(s).</li>
    </ol>
</div>










                   
                </tbody>
            </table>




            
        </div>