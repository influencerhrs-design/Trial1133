fetch("jobs.json")
  .then(res => res.json())
  .then(data => {
    let jobsDiv = document.getElementById("jobs");

    data.forEach(job => {
      if (job.approved) {
        jobsDiv.innerHTML += `
          <div class="job-card">
            <h3>${job.title}</h3>
            <p><b>Company:</b> ${job.company}</p>
            <p><b>Location:</b> ${job.location}</p>
            <p>${job.description}</p>
            <p><b>Deadline:</b> ${job.deadline}</p>

            <a href="mailto:${job.company_email}">Apply by Email</a> |
            <a href="https://wa.me/${job.whatsapp}" target="_blank">
              Apply via WhatsApp
            </a>
          </div>
        `;
      }
    });
  });
