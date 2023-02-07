import React,{ useState, useRef, useEffect } from 'react';
import { Link } from "react-router-dom";


import { DropdownBlog } from "./../Concierge";
import pic3 from './../../../../images/avatar/pic3.jpg';
import pic5 from './../../../../images/avatar/pic5.jpg';

const Active = ()=>{
	const [data, setData] = useState(
		document.querySelectorAll("#example_wrapper tbody tr")
	);
	const sort = 10;
	const activePag = useRef(0);
	const [test, settest] = useState(0);

	// Active data
	const chageData = (frist, sec) => {
		for (var i = 0; i < data.length; ++i) {
			if (i >= frist && i < sec) {
				data[i].classList.remove("d-none");
			} else {
				data[i].classList.add("d-none");
			}
		}
	};
   // use effect
   useEffect(() => {
      setData(document.querySelectorAll("#example_wrapper tbody tr"));
      //chackboxFun();
	}, [test]);

  
   // Active pagginarion
   activePag.current === 0 && chageData(0, sort);
   // paggination
   let paggination = Array(Math.ceil(data.length / sort))
      .fill()
      .map((_, i) => i + 1);

   // Active paggination & chage data
	const onClick = (i) => {
		activePag.current = i;
		chageData(activePag.current * sort, (activePag.current + 1) * sort);
		settest(i);
	};
  
	const chackbox = document.querySelectorAll(".sorting_1 input");
	const motherChackBox = document.querySelector(".sorting_asc input");
   // console.log(document.querySelectorAll(".sorting_1 input")[0].checked);
	const chackboxFun = (type) => {
      for (let i = 0; i < chackbox.length; i++) {
         const element = chackbox[i];
         if (type === "all") {
            if (motherChackBox.checked) {
               element.checked = true;
            } else {
               element.checked = false;
            }
         } else {
            if (!element.checked) {
               motherChackBox.checked = false;
               break;
            } else {
               motherChackBox.checked = true;
            }
         }
      }
    };
	return(
		<>
			<div className="table-responsive">
				<div id="example_wrapper" className="dataTables_wrapper no-footer">
					<table
						id="example2"
						className="table card-table default-table display mb-4 dataTablesCard dataTable no-footer"
					>
						<thead>
							<tr role="row">
								<th className="sorting_asc bg-none" >
									<div className="form-check  style-1">
										<input type="checkbox" onClick={() => chackboxFun("all")} className="form-check-input" id="checkAll" required=""/>
									</div>
								</th>
								<th className="sorting_asc">Employee Name</th>
								<th className="sorting_1">Job Desk</th>
								<th className="sorting_1">Schedule</th>
								<th className="sorting_1">Contact</th>
								<th className="sorting_1">Status</th>
								<th className="sorting_1 bg-none"></th>
							</tr>
						</thead>
						<tbody>
							<tr role="row" className="odd">
								<td className="sorting_1">  
									<div className="form-check style-1">
										<input type="checkbox" onClick={() => chackboxFun()}
											className="form-check-input" id="customCheckBox22" required=""
										/>
									</div> 
								</td>
								<td>
									<div className="concierge-bx">
										<img className="me-3 rounded" src={pic3} alt="" />
										<div>
											<span className="text-primary">#EMP-00025</span>
											<h4 className="mt-1"><Link className="text-black" to={"./guest-detail"}>Margaretha</Link></h4>
											<span className="fs-14">Join on January 21th, 2015</span>
										</div>
									</div>
								</td>
								<td className="job-desk">
									<div>
										<span className="fs-16">Act as a liaison between guests and any department necessary including the kitchen, housekeeping, etc</span>
									</div>
								</td>
								<td>
									<div>
										<h5 className="font-w600">Tuesday, Friday, Sunday</h5>
										<span>08:00 AM - 04:00 PM</span>
									</div>
								</td>
								<td>
									<div className="text-nowrap">
										<span className="text-black font-w500"><i className="fas fa-phone-volume me-2 text-primary"></i>+12 3456 678</span>
									</div>
								</td>
								<td><span className="text-success font-w600">ACTIVE</span></td>
								<td><DropdownBlog /></td>
							</tr>
							<tr role="row" className="even">	
								<td className="sorting_1">  
									<div className="form-check  style-1">
										<input type="checkbox" onClick={() => chackboxFun()}
											className="form-check-input" id="customCheckBox24" required=""
										/>
									</div> 
								</td>
								<td>
									<div className="concierge-bx">
										<img className="me-3 rounded" src={pic5} alt="" />
										<div>
											<span className="text-primary">#EMP-00025</span>
											<h4 className="mt-1"><Link className="text-black" to={"./guest-detail"}>Margaretha</Link></h4>
											<span className="fs-14">Join on January 21th, 2015</span>
										</div>
									</div>
								</td>
								<td className="job-desk">
									<div>
										<span className="fs-16">Act as a liaison between guests and any department necessary including the kitchen, housekeeping, etc</span>
									</div>
								</td>
								<td>
									<div>
										<h5 className="font-w600">Tuesday, Friday, Sunday</h5>
										<span>08:00 AM - 04:00 PM</span>
									</div>
								</td>
								<td>
									<div className="text-nowrap">
										<span className="text-black font-w500"><i className="fas fa-phone-volume me-2 text-primary"></i>+12 3456 678</span>
									</div>
								</td>
								<td><span className="text-success font-w600">ACTIVE</span></td>
								<td><DropdownBlog /></td>
							</tr>							
						</tbody>
					</table>
					<div className="d-sm-flex text-center justify-content-between align-items-center mt-3">
						<div className="dataTables_info">
							Showing {activePag.current * sort + 1} to{" "}
							{data.length > (activePag.current + 1) * sort
								? (activePag.current + 1) * sort
								: data.length}{" "}
							of {data.length} entries
						</div>
						<div
							className="dataTables_paginate paging_simple_numbers"
							id="example2_paginate"
						>
							<Link
								className="paginate_button previous "
								to="/concierge"
								onClick={() =>
								   activePag.current > 0 &&
								   onClick(activePag.current - 1)
								}
							 >
								<i className="fa fa-angle-double-left" aria-hidden="true"></i> Previous
							</Link>
							<span>
								{paggination.map((number, i) => (
								   <Link
									  key={i}
									  to="/concierge"
									  className={`paginate_button  ${
										 activePag.current === i ? "current" : ""
									  } `}
									  onClick={() => onClick(i)}
								   >
									  {number}
								   </Link>
								))}
							</span>

							<Link
								className="paginate_button next"
								to="/concierge"
								onClick={() =>
								   activePag.current + 1 < paggination.length &&
								   onClick(activePag.current + 1)
								}
							>
								Next <i className="fa fa-angle-double-right" aria-hidden="true"></i>
							</Link>
						</div>
					</div>
				</div>
			</div>
		</>
	)
}
export default Active;